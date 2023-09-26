<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Exception as CoreException;
use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Persistence;
use Atk4\Ui\App;
use Atk4\Ui\Callback;
use Atk4\Ui\Exception;
use Atk4\Ui\Exception\UnhandledCallbackExceptionError;
use Atk4\Ui\Layout;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Test if all demos can be rendered successfully and test some expected data.
 *
 * Requests are emulated in the same process. It is fast, but some output or shutdown functionality cannot be fully tested.
 */
class DemosTest extends TestCase
{
    protected const ROOT_DIR = __DIR__ . '/..';
    protected const DEMOS_DIR = self::ROOT_DIR . '/demos';

    private static array $_serverSuperglobalBackup;

    private static ?Persistence $_db = null;

    private static array $_failedParentTests = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$_serverSuperglobalBackup = $_SERVER;
    }

    public static function tearDownAfterClass(): void
    {
        $_SERVER = self::$_serverSuperglobalBackup;

        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (self::$_db === null) {
            // load demos config
            $initVars = get_defined_vars();
            $this->setSuperglobalsFromRequest(new Request('GET', 'http://localhost/demos/?APP_CALL_EXIT=0&APP_CATCH_EXCEPTIONS=0&APP_ALWAYS_RUN=0'));

            /** @var App $app */
            $app = 'for-phpstan'; // @phpstan-ignore-line
            require_once static::DEMOS_DIR . '/init-app.php';
            $initVars = array_diff_key(get_defined_vars(), $initVars + ['initVars' => true]);

            if (array_keys($initVars) !== ['app']) {
                throw new Exception('Demos init must setup only $app variable');
            }

            self::$_db = $app->db;

            // prevent $app to run on shutdown
            $app->runCalled = true;
        }
    }

    protected function onNotSuccessfulTest(\Throwable $t): void
    {
        if (!in_array($this->getStatus(), [
            \PHPUnit\Runner\BaseTestRunner::STATUS_PASSED,
            \PHPUnit\Runner\BaseTestRunner::STATUS_SKIPPED,
            \PHPUnit\Runner\BaseTestRunner::STATUS_INCOMPLETE,
        ], true)) {
            if (!isset(self::$_failedParentTests[$this->getName()])) {
                self::$_failedParentTests[$this->getName()] = $this->getStatus();
            } else {
                self::markTestIncomplete('Test failed, but non-HTTP test failed too, fix it first');
            }
        }

        throw $t;
    }

    protected function setSuperglobalsFromRequest(RequestInterface $request): void
    {
        $this->resetSuperglobals();

        $rootDirRealpath = realpath(static::ROOT_DIR);

        $requestPath = $request->getUri()->getPath();
        $requestQuery = $request->getUri()->getQuery();
        $_SERVER = [
            'REQUEST_METHOD' => $request->getMethod(),
            'REQUEST_URI' => $requestPath . ($requestQuery !== '' ? '?' . $requestQuery : ''),
            'QUERY_STRING' => $requestQuery,
            'DOCUMENT_ROOT' => $rootDirRealpath,
            'SCRIPT_FILENAME' => $rootDirRealpath . $requestPath,
        ];
        foreach (array_keys($request->getHeaders()) as $k) {
            $kSever = 'HTTP_' . str_replace('-', '_', strtoupper($k));
            $_SERVER[$kSever] = $request->getHeaderLine($k);
        }

        $_GET = [];
        parse_str($requestQuery, $queryArr);
        foreach ($queryArr as $k => $v) {
            $_GET[$k] = $v;
        }

        $_POST = [];
        parse_str($request->getBody()->getContents(), $queryArr);
        foreach ($queryArr as $k => $v) {
            $_POST[$k] = $v;
        }
    }

    protected function resetSuperglobals(): void
    {
        unset($_SERVER);
        unset($_GET);
        unset($_POST);
        unset($_FILES);
        unset($_COOKIE);
        unset($_SESSION);
    }

    protected function createTestingApp(): App
    {
        $app = new class(['callExit' => false, 'catchExceptions' => false, 'alwaysRun' => false]) extends App {
            public function callExit(): void
            {
                throw new DemosTestExitError();
            }

            protected function emitResponse(): void {}
        };
        $app->initLayout([Layout::class]);

        // clone DB (mainly because all Models remains attached now, TODO can be removed once they are GCed)
        $app->db = clone self::$_db;

        return $app;
    }

    protected function assertNoGlobalSticky(App $app): void
    {
        $appSticky = array_diff_assoc(
            \Closure::bind(static fn () => $app->stickyGetArguments, null, App::class)(),
            ['__atk_json' => false, '__atk_tab' => false, 'APP_CALL_EXIT' => true, 'APP_CATCH_EXCEPTIONS' => true]
        );
        if ($appSticky !== []) {
            throw (new Exception('Global GET sticky must never be set by any component'))
                ->addMoreInfo('appSticky', $appSticky);
        }
    }

    protected function getClient(): Client
    {
        $handler = function (RequestInterface $request) {
            // emulate request
            $localPath = static::ROOT_DIR . $request->getUri()->getPath();
            $this->setSuperglobalsFromRequest($request);

            ob_start();
            try {
                $app = $this->createTestingApp();
                $this->resetSuperglobals();
                try {
                    require $localPath;

                    if (!$app->runCalled) {
                        $app->run();
                    }

                    $this->assertNoGlobalSticky($app);
                } catch (DemosTestExitError $e) {
                }
            } finally {
                self::assertSame('', ob_get_clean());
                $this->resetSuperglobals();
            }

            // rewind the body of the response if possible
            if ($app->getResponse()->getBody()->isSeekable()) {
                $app->getResponse()->getBody()->rewind();
            }

            return new \GuzzleHttp\Promise\FulfilledPromise($app->getResponse());
        };

        return new Client(['base_uri' => 'http://localhost/', 'handler' => $handler]);
    }

    protected function getResponseFromRequest(string $path, array $options = []): ResponseInterface
    {
        try {
            return $this->getClient()->request(isset($options['form_params']) ? 'POST' : 'GET', $this->getPathWithAppVars($path), $options);
        } catch (\GuzzleHttp\Exception\ServerException $ex) {
            $exFactoryWithFullBody = new class('', $ex->getRequest()) extends \GuzzleHttp\Exception\RequestException {
                public static function getResponseBodySummary(ResponseInterface $response): string
                {
                    $body = $response->getBody();
                    $res = $body->getContents();

                    if ($body->isSeekable()) {
                        $body->rewind();
                    }

                    return $res;
                }
            };

            throw $exFactoryWithFullBody::create($ex->getRequest(), $ex->getResponse());
        }
    }

    protected function getResponseFromRequest5xx(string $path, array $options = []): ResponseInterface
    {
        try {
            $response = $this->getResponseFromRequest($path, $options);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $response = $e->getResponse();
        } catch (UnhandledCallbackExceptionError $e) {
            while ($e instanceof UnhandledCallbackExceptionError) {
                $e = $e->getPrevious();
            }

            throw $e;
        }

        return $response;
    }

    protected function getPathWithAppVars(string $path): string
    {
        return 'demos/' . $path;
    }

    /** @var string */
    protected $regexHtml = '~^<!DOCTYPE html>\s*<html~';
    /** @var string */
    protected $regexJson = '~
        (?(DEFINE)
           (?<number>   -? (?= [1-9]|0(?!\d) ) \d+ (\.\d+)? ([eE] [+-]? \d+)? )
           (?<boolean>   true | false | null )
           (?<string>    " ([^"\\\\]* | \\\\ ["\\\\bfnrt/] | \\\\ u [0-9a-f]{4} )* " )
           (?<array>     \[  (?:  (?&json)  (?: , (?&json)  )*  )?  \s* \] )
           (?<pair>      \s* (?&string) \s* : (?&json)  )
           (?<object>    \{  (?:  (?&pair)  (?: , (?&pair)  )*  )?  \s* \} )
           (?<json>   \s* (?: (?&number) | (?&boolean) | (?&string) | (?&array) | (?&object) ) \s* )
        )
        \A (?&json) \Z
        ~six';
    /** @var string */
    protected $regexSse = '~^(id|event|data).*$~m';

    public function provideDemosStatusAndHtmlResponseCases(): iterable
    {
        $excludeDirs = ['_demo-data', '_includes'];
        $excludeFiles = ['_unit-test/stream.php', 'layout/layouts_error.php'];

        $files = [];
        $files[] = 'index.php';
        foreach (array_diff(scandir(static::DEMOS_DIR), ['.', '..'], $excludeDirs) as $dir) {
            if (!is_dir(static::DEMOS_DIR . '/' . $dir)) {
                continue;
            }

            foreach (scandir(static::DEMOS_DIR . '/' . $dir) as $f) {
                $path = $dir . '/' . $f;
                if (substr($path, -4) !== '.php' || in_array($path, $excludeFiles, true)) {
                    continue;
                }

                $files[] = $path;
            }
        }

        // these tests require SessionTrait, more precisely session_start() which we do not support in non-HTTP testing
        // always move these tests to the end, so data provider # stays same as much as possible across tests for fast skip
        $httpOnlyFiles = ['collection/tablefilter.php', 'interactive/popup.php'];
        foreach ($files as $k => $path) {
            if (in_array($path, $httpOnlyFiles, true)) {
                unset($files[$k]);
                $files[] = $path;
            }
        }
        if (static::class === self::class) {
            foreach ($files as $k => $path) {
                if (in_array($path, $httpOnlyFiles, true)) {
                    unset($files[$k]);
                }
            }
        }

        foreach ($files as $path) {
            yield [$path];
        }
    }

    /**
     * @dataProvider provideDemosStatusAndHtmlResponseCases
     */
    public function testDemosStatusAndHtmlResponse(string $path): void
    {
        $response = $this->getResponseFromRequest($path);
        self::assertSame(200, $response->getStatusCode());
        self::assertMatchesRegularExpression($this->regexHtml, $response->getBody()->getContents());
    }

    public function testDemoResponseError(): void
    {
        if (static::class === self::class) {
            $this->expectException(CoreException::class);
            $this->expectExceptionMessage('Property for specified object is not defined');
        }

        $response = $this->getResponseFromRequest5xx('layout/layouts_error.php');

        self::assertSame(500, $response->getStatusCode());
        self::assertStringContainsString('Property for specified object is not defined', $response->getBody()->getContents());
    }

    public function provideDemoGetCases(): iterable
    {
        yield ['others/sticky.php?xx=YEY'];
        yield ['others/sticky.php?c=OHO'];
        yield ['others/sticky.php?xx=YEY&c=OHO'];
    }

    /**
     * @dataProvider provideDemoGetCases
     */
    public function testDemoGet(string $path): void
    {
        $response = $this->getResponseFromRequest($path);
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('text/html', preg_replace('~;\s*charset=.+$~', '', $response->getHeaderLine('Content-Type')));
        self::assertMatchesRegularExpression($this->regexHtml, $response->getBody()->getContents());
    }

    public function testHugeOutputStream(): void
    {
        $sizeMb = 40;
        $sizeBytes = $sizeMb * 1024 * 1024;
        $response = $this->getResponseFromRequest('_unit-test/stream.php?size_mb=' . $sizeMb);
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/octet-stream', $response->getHeaderLine('Content-Type'));
        self::assertSame((string) $sizeBytes, $response->getHeaderLine('Content-Length'));

        $hugePseudoStreamFx = static function (int $pos) {
            return "\n\0" . str_repeat($pos . ',', 1024);
        };
        $pos = 0;
        while ($pos < $sizeBytes) {
            $buffer = $hugePseudoStreamFx($pos);
            $length = strlen($buffer);
            if ($pos + $length > $sizeBytes) {
                $length = $sizeBytes - $pos;
                $buffer = substr($buffer, 0, $length);
            }
            $pos += $length;

            if ($buffer !== $response->getBody()->read($length)) {
                self::assertSame(-1, $pos);
            }
        }
    }

    public function testWizard(): void
    {
        // this test requires SessionTrait, more precisely session_start() which we do not support in non-HTTP testing
        if (static::class === self::class) {
            self::assertTrue(true); // @phpstan-ignore-line

            return;
        }

        $response = $this->getResponseFromRequest(
            'interactive/wizard.php?demo_wizard=1&' . Callback::URL_QUERY_TRIGGER_PREFIX . 'w_form_submit=ajax&' . Callback::URL_QUERY_TARGET . '=w_form_submit',
            ['form_params' => [
                'dsn' => 'mysql://root:root@db-host.example.com/atk4',
            ]]
        );

        self::assertSame(200, $response->getStatusCode());
        self::assertMatchesRegularExpression($this->regexJson, $response->getBody()->getContents());

        $response = $this->getResponseFromRequest('interactive/wizard.php?atk_admin_wizard=2&name=Country');
        self::assertSame(200, $response->getStatusCode());
        self::assertMatchesRegularExpression($this->regexHtml, $response->getBody()->getContents());
    }

    public function provideDemoAssertJsonResponseCases(): iterable
    {
        // simple reload
        yield ['_unit-test/reload.php?__atk_reload=reload'];
        // loader callback reload
        yield ['_unit-test/reload.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'c_reload=ajax&' . Callback::URL_QUERY_TARGET . '=c_reload'];
        // test catch exceptions
        yield ['_unit-test/exception.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'm_cb=ajax&' . Callback::URL_QUERY_TARGET . '=m_cb&__atk_json=1', 'Test throw exception!'];
        yield ['_unit-test/exception.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'm2_cb=ajax&' . Callback::URL_QUERY_TARGET . '=m2_cb&__atk_json=1', 'Test trigger error!'];
    }

    /**
     * Test reload and loader callback.
     *
     * @dataProvider provideDemoAssertJsonResponseCases
     */
    public function testDemoAssertJsonResponse(string $path, string $expectedExceptionMessage = null): void
    {
        if (static::class === self::class) {
            if ($expectedExceptionMessage !== null) {
                if (str_contains($path, '=m2_cb&')) {
                    self::assertTrue(true); // @phpstan-ignore-line

                    return;
                }

                $this->expectExceptionMessage($expectedExceptionMessage);
            }
        }

        $response = $this->getResponseFromRequest5xx($path);
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/json', preg_replace('~;\s*charset=.+$~', '', $response->getHeaderLine('Content-Type')));
        $responseBodyStr = $response->getBody()->getContents();
        self::assertMatchesRegularExpression($this->regexJson, $responseBodyStr);
        self::assertStringNotContainsString(preg_replace('~.+\\\\~', '', UnhandledCallbackExceptionError::class), $responseBodyStr);
        if ($expectedExceptionMessage !== null) {
            self::assertStringContainsString($expectedExceptionMessage, $responseBodyStr);
        }
    }

    public function provideDemoAssertSseResponseCases(): iterable
    {
        yield ['_unit-test/sse.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'see_test=ajax&' . Callback::URL_QUERY_TARGET . '=1&__atk_sse=1'];
        yield ['_unit-test/console.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'console_test=ajax&' . Callback::URL_QUERY_TARGET . '=1&__atk_sse=1'];
        yield ['_unit-test/console_run.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'console_test=ajax&' . Callback::URL_QUERY_TARGET . '=1&__atk_sse=1'];
        yield ['_unit-test/console_exec.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'console_test=ajax&' . Callback::URL_QUERY_TARGET . '=1&__atk_sse=1'];
    }

    /**
     * Test JsSse and Console.
     *
     * @dataProvider provideDemoAssertSseResponseCases
     */
    public function testDemoAssertSseResponse(string $path): void
    {
        // this test requires SessionTrait, more precisely session_start() which we do not support in non-HTTP testing
        if (static::class === self::class) {
            self::assertTrue(true); // @phpstan-ignore-line

            return;
        }

        $response = $this->getResponseFromRequest($path);
        self::assertSame(200, $response->getStatusCode());

        $outputLines = preg_split('~\r?\n|\r~', $response->getBody()->getContents(), -1, \PREG_SPLIT_NO_EMPTY);

        // check SSE Syntax
        self::assertGreaterThan(0, count($outputLines));
        foreach ($outputLines as $index => $line) {
            preg_match_all($this->regexSse, $line, $matchesAll);
            self::assertSame(
                $line,
                implode('', $matchesAll[0] ?? ['error']),
                'Testing SSE response line ' . $index . ' with content ' . $line
            );
        }
    }

    public function provideDemoAssertJsonResponsePostCases(): iterable
    {
        yield [
            '_unit-test/post.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'test_submit=ajax&' . Callback::URL_QUERY_TARGET . '=test_submit',
            ['f1' => 'v1'],
        ];
    }

    /**
     * @dataProvider provideDemoAssertJsonResponsePostCases
     */
    public function testDemoAssertJsonResponsePost(string $path, array $postData): void
    {
        $response = $this->getResponseFromRequest($path, ['form_params' => $postData]);
        self::assertSame(200, $response->getStatusCode());
        self::assertMatchesRegularExpression($this->regexJson, $response->getBody()->getContents());
    }

    /**
     * @dataProvider provideDemoCallbackErrorCases
     */
    public function testDemoCallbackError(string $path, string $expectedExceptionMessage): void
    {
        if (static::class === self::class) {
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $response = $this->getResponseFromRequest5xx($path);

        self::assertSame(500, $response->getStatusCode());
        $responseBodyStr = $response->getBody()->getContents();
        self::assertStringNotContainsString(preg_replace('~.+\\\\~', '', UnhandledCallbackExceptionError::class), $responseBodyStr);
        self::assertStringContainsString($expectedExceptionMessage, $responseBodyStr);
    }

    public function provideDemoCallbackErrorCases(): iterable
    {
        yield [
            '_unit-test/callback-nested.php?err_sub_loader&' . Callback::URL_QUERY_TRIGGER_PREFIX . 'trigger_main_loader=callback&' . Callback::URL_QUERY_TARGET . '=non_existing_target',
            'Callback requested, but never reached. You may be missing some arguments in request URL.',
        ];
        yield [
            '_unit-test/callback-nested.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'trigger_main_loader=callback&' . Callback::URL_QUERY_TRIGGER_PREFIX . 'trigger_sub_loader=callback&' . Callback::URL_QUERY_TARGET . '=non_existing_target',
            'Callback requested, but never reached. You may be missing some arguments in request URL.',
        ];
        yield [
            '_unit-test/callback-nested.php?err_main_loader&' . Callback::URL_QUERY_TRIGGER_PREFIX . 'trigger_main_loader=callback&' . Callback::URL_QUERY_TARGET . '=trigger_main_loader',
            'Exception from Main Loader',
        ];
        yield [
            '_unit-test/callback-nested.php?err_sub_loader&' . Callback::URL_QUERY_TRIGGER_PREFIX . 'trigger_main_loader=callback&' . Callback::URL_QUERY_TRIGGER_PREFIX . 'trigger_sub_loader=callback&' . Callback::URL_QUERY_TARGET . '=trigger_sub_loader',
            'Exception from Sub Loader',
        ];
        yield [
            '_unit-test/callback-nested.php?err_sub_loader2&' . Callback::URL_QUERY_TRIGGER_PREFIX . 'trigger_main_loader=callback&' . Callback::URL_QUERY_TRIGGER_PREFIX . 'trigger_sub_loader=callback&' . Callback::URL_QUERY_TARGET . '=trigger_sub_loader',
            'Exception II from Sub Loader',
        ];
    }
}

class DemosTestExitError extends \Error {}
