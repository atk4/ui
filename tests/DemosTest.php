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
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
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
            $app = 'for-phpstan';
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
                static::markTestIncomplete('Test failed, but non-HTTP test failed too, fix it first');
            }
        }

        throw $t;
    }

    protected function setSuperglobalsFromRequest(RequestInterface $request): void
    {
        $this->resetSuperglobals();

        $_SERVER = [
            'REQUEST_METHOD' => $request->getMethod(),
            'HTTP_HOST' => $request->getUri()->getHost(),
            'REQUEST_URI' => (string) $request->getUri(),
            'QUERY_STRING' => $request->getUri()->getQuery(),
            'DOCUMENT_ROOT' => realpath(static::ROOT_DIR),
            'SCRIPT_FILENAME' => realpath(static::ROOT_DIR) . $request->getUri()->getPath(),
        ];

        $_GET = [];
        parse_str($request->getUri()->getQuery(), $queryArr);
        foreach ($queryArr as $k => $v) {
            $_GET[$k] = $v;
        }

        $_POST = [];
        parse_str($request->getBody()->getContents(), $queryArr);
        foreach ($queryArr as $k => $v) {
            $_POST[$k] = $v;
        }

        \Closure::bind(function () {
            App::$_sentHeaders = [];
        }, null, App::class)();
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
        };
        $app->initLayout([Layout\Maestro::class]);

        // clone DB (mainly because all Models remains attached now, TODO can be removed once they are GCed)
        $app->db = clone self::$_db;

        return $app;
    }

    protected function assertNoGlobalSticky(App $app): void
    {
        $appSticky = array_diff_assoc(
            \Closure::bind(fn () => $app->stickyGetArguments, null, App::class)(),
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
                require $localPath;

                if (!$app->runCalled) {
                    $app->run();
                }

                $this->assertNoGlobalSticky($app);
            } catch (\Throwable $e) {
                // session_start() or ini_set() functions can be used only with native HTTP tests
                // override test expectation here to finish there tests cleanly (TODO better to make the code testable without calling these functions)
                // TODO impl. volatile session manager for unit testing
                if ($e instanceof \ErrorException && preg_match('~^(session_start|ini_set)\(\).* headers already sent$~', $e->getMessage())) {
                    $this->expectExceptionObject($e);
                }

                if (!$e instanceof DemosTestExitError) {
                    throw $e;
                }
            } finally {
                $body = ob_get_clean();
                $this->resetSuperglobals();
            }

            [$statusCode, $headers] = \Closure::bind(function () {
                $statusCode = 200;
                $headers = App::$_sentHeaders;
                if (isset($headers[App::HEADER_STATUS_CODE])) {
                    $statusCode = $headers[App::HEADER_STATUS_CODE];
                    unset($headers[App::HEADER_STATUS_CODE]);
                }

                return [$statusCode, $headers];
            }, null, App::class)();

            // Attach a response to the easy handle with the parsed headers.
            $response = new Response(
                $statusCode,
                $headers,
                class_exists(Utils::class) ? Utils::streamFor($body) : \GuzzleHttp\Psr7\stream_for($body), // @phpstan-ignore-line Utils class present since guzzlehttp/psr7 v1.7
                '1.0'
            );

            // Rewind the body of the response if possible.
            $body = $response->getBody();
            if ($body->isSeekable()) {
                $body->rewind();
            }

            return new \GuzzleHttp\Promise\FulfilledPromise($response);
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
    protected $regexHtml = '~^<!DOCTYPE~';
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

    public function demoFilesProvider(): array
    {
        $excludeDirs = ['_demo-data', '_includes'];
        $excludeFiles = ['layout/layouts_error.php'];

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

        return array_map(fn (string $v) => [$v], $files);
    }

    /**
     * @dataProvider demoFilesProvider
     */
    public function testDemosStatusAndHtmlResponse(string $path): void
    {
        $response = $this->getResponseFromRequest($path);
        static::assertSame(200, $response->getStatusCode());
        static::assertMatchesRegularExpression($this->regexHtml, $response->getBody()->getContents());
    }

    public function testDemoResponseError(): void
    {
        if (static::class === self::class) {
            $this->expectException(CoreException::class);
            $this->expectExceptionMessage('Property for specified object is not defined');
        }

        $response = $this->getResponseFromRequest5xx('layout/layouts_error.php');

        static::assertSame(500, $response->getStatusCode());
        static::assertStringContainsString('Property for specified object is not defined', $response->getBody()->getContents());
    }

    public function casesDemoGetProvider(): array
    {
        $files = [];
        $files[] = ['others/sticky.php?xx=YEY'];
        $files[] = ['others/sticky.php?c=OHO'];
        $files[] = ['others/sticky.php?xx=YEY&c=OHO'];

        return $files;
    }

    /**
     * @dataProvider casesDemoGetProvider
     */
    public function testDemoGet(string $path): void
    {
        $response = $this->getResponseFromRequest($path);
        static::assertSame(200, $response->getStatusCode());
        static::assertSame('text/html', preg_replace('~;\s*charset=.+$~', '', $response->getHeaderLine('Content-Type')));
        static::assertMatchesRegularExpression($this->regexHtml, $response->getBody()->getContents());
    }

    public function testWizard(): void
    {
        // this test requires SessionTrait, more precisely session_start() which we do not support in non-HTTP testing
        if (static::class === self::class) {
            static::assertTrue(true);

            return;
        }

        $response = $this->getResponseFromRequest(
            'interactive/wizard.php?demo_wizard=1&' . Callback::URL_QUERY_TRIGGER_PREFIX . 'w_form_submit=ajax&' . Callback::URL_QUERY_TARGET . '=w_form_submit',
            ['form_params' => [
                'dsn' => 'mysql://root:root@db-host.example.com/atk4',
            ]]
        );

        static::assertSame(200, $response->getStatusCode());
        static::assertMatchesRegularExpression($this->regexJson, $response->getBody()->getContents());

        $response = $this->getResponseFromRequest('interactive/wizard.php?atk_admin_wizard=2&name=Country');
        static::assertSame(200, $response->getStatusCode());
        static::assertMatchesRegularExpression($this->regexHtml, $response->getBody()->getContents());
    }

    /**
     * Test reload and loader callback.
     */
    public function jsonResponseProvider(): array
    {
        $files = [];
        // simple reload
        $files[] = ['_unit-test/reload.php?__atk_reload=reload'];
        // loader callback reload
        $files[] = ['_unit-test/reload.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'c_reload=ajax&' . Callback::URL_QUERY_TARGET . '=c_reload'];
        // test catch exceptions
        $files[] = ['_unit-test/exception.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'm_cb=ajax&' . Callback::URL_QUERY_TARGET . '=m_cb&__atk_json=1', 'Test throw exception!'];
        $files[] = ['_unit-test/exception.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'm2_cb=ajax&' . Callback::URL_QUERY_TARGET . '=m2_cb&__atk_json=1', 'Test trigger error!'];

        return $files;
    }

    /**
     * @dataProvider jsonResponseProvider
     */
    public function testDemoAssertJsonResponse(string $path, string $expectedExceptionMessage = null): void
    {
        if (static::class === self::class) {
            if ($expectedExceptionMessage !== null) {
                if (str_contains($path, '=m2_cb&')) {
                    static::assertTrue(true);

                    return;
                }

                $this->expectExceptionMessage($expectedExceptionMessage);
            }
        }

        $response = $this->getResponseFromRequest5xx($path);
        static::assertSame(200, $response->getStatusCode());
        static::assertSame('application/json', preg_replace('~;\s*charset=.+$~', '', $response->getHeaderLine('Content-Type')));
        $responseBodyStr = $response->getBody()->getContents();
        static::assertMatchesRegularExpression($this->regexJson, $responseBodyStr);
        static::assertStringNotContainsString(preg_replace('~.+\\\\~', '', UnhandledCallbackExceptionError::class), $responseBodyStr);
        if ($expectedExceptionMessage !== null) {
            static::assertStringContainsString($expectedExceptionMessage, $responseBodyStr);
        }
    }

    /**
     * Test JsSse and Console.
     */
    public function sseResponseProvider(): array
    {
        $files = [];
        $files[] = ['_unit-test/sse.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'see_test=ajax&' . Callback::URL_QUERY_TARGET . '=1&__atk_sse=1'];
        $files[] = ['_unit-test/console.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'console_test=ajax&' . Callback::URL_QUERY_TARGET . '=1&__atk_sse=1'];
        $files[] = ['_unit-test/console_run.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'console_test=ajax&' . Callback::URL_QUERY_TARGET . '=1&__atk_sse=1'];
        $files[] = ['_unit-test/console_exec.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'console_test=ajax&' . Callback::URL_QUERY_TARGET . '=1&__atk_sse=1'];

        return $files;
    }

    /**
     * @dataProvider sseResponseProvider
     */
    public function testDemoAssertSseResponse(string $path): void
    {
        // this test requires SessionTrait, more precisely session_start() which we do not support in non-HTTP testing
        if (static::class === self::class) {
            static::assertTrue(true);

            return;
        }

        $response = $this->getResponseFromRequest($path);
        static::assertSame(200, $response->getStatusCode());

        $outputLines = preg_split('~\r?\n|\r~', $response->getBody()->getContents(), -1, \PREG_SPLIT_NO_EMPTY);

        // check SSE Syntax
        static::assertGreaterThan(0, count($outputLines));
        foreach ($outputLines as $index => $line) {
            preg_match_all($this->regexSse, $line, $matchesAll);
            $format_match_string = implode('', $matchesAll[0] ?? ['error']);

            static::assertSame(
                $line,
                $format_match_string,
                'Testing SSE response line ' . $index . ' with content ' . $line
            );
        }
    }

    public function jsonResponsePostProvider(): array
    {
        $files = [];
        $files[] = [
            '_unit-test/post.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'test_submit=ajax&' . Callback::URL_QUERY_TARGET . '=test_submit',
            [
                'f1' => 'v1',
            ],
        ];

        return $files;
    }

    /**
     * @dataProvider jsonResponsePostProvider
     */
    public function testDemoAssertJsonResponsePost(string $path, array $postData): void
    {
        $response = $this->getResponseFromRequest($path, ['form_params' => $postData]);
        static::assertSame(200, $response->getStatusCode());
        static::assertMatchesRegularExpression($this->regexJson, $response->getBody()->getContents());
    }

    /**
     * @dataProvider demoCallbackErrorProvider
     */
    public function testDemoCallbackError(string $path, string $expectedExceptionMessage): void
    {
        if (static::class === self::class) {
            $this->expectExceptionMessage($expectedExceptionMessage);
        }

        $response = $this->getResponseFromRequest5xx($path);

        static::assertSame(500, $response->getStatusCode());
        $responseBodyStr = $response->getBody()->getContents();
        static::assertStringNotContainsString(preg_replace('~.+\\\\~', '', UnhandledCallbackExceptionError::class), $responseBodyStr);
        static::assertStringContainsString($expectedExceptionMessage, $responseBodyStr);
    }

    public function demoCallbackErrorProvider(): array
    {
        return [
            [
                '_unit-test/callback-nested.php?err_sub_loader&' . Callback::URL_QUERY_TRIGGER_PREFIX . 'trigger_main_loader=callback&' . Callback::URL_QUERY_TARGET . '=non_existing_target',
                'Callback requested, but never reached. You may be missing some arguments in request URL.',
            ],
            [
                '_unit-test/callback-nested.php?' . Callback::URL_QUERY_TRIGGER_PREFIX . 'trigger_main_loader=callback&' . Callback::URL_QUERY_TRIGGER_PREFIX . 'trigger_sub_loader=callback&' . Callback::URL_QUERY_TARGET . '=non_existing_target',
                'Callback requested, but never reached. You may be missing some arguments in request URL.',
            ],
            [
                '_unit-test/callback-nested.php?err_main_loader&' . Callback::URL_QUERY_TRIGGER_PREFIX . 'trigger_main_loader=callback&' . Callback::URL_QUERY_TARGET . '=trigger_main_loader',
                'Exception from Main Loader',
            ],
            [
                '_unit-test/callback-nested.php?err_sub_loader&' . Callback::URL_QUERY_TRIGGER_PREFIX . 'trigger_main_loader=callback&' . Callback::URL_QUERY_TRIGGER_PREFIX . 'trigger_sub_loader=callback&' . Callback::URL_QUERY_TARGET . '=trigger_sub_loader',
                'Exception from Sub Loader',
            ],
            [
                '_unit-test/callback-nested.php?err_sub_loader2&' . Callback::URL_QUERY_TRIGGER_PREFIX . 'trigger_main_loader=callback&' . Callback::URL_QUERY_TRIGGER_PREFIX . 'trigger_sub_loader=callback&' . Callback::URL_QUERY_TARGET . '=trigger_sub_loader',
                'Exception II from Sub Loader',
            ],
        ];
    }
}

class DemosTestExitError extends \Error
{
}
