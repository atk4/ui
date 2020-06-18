<?php

declare(strict_types=1);

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;
use atk4\ui\App;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Test if all demos can be rendered successfully and test some expected data.
 *
 * Requests are emulated in the same process. It is fast, but some output or shutdown functionality can not be fully tested.
 */
class DemosTest extends AtkPhpunit\TestCase
{
    /** @var App Initialized App instance with working DB connection */
    private static $_db;

    /** @var string */
    protected $demosDir = __DIR__ . '/../demos';

    protected function setUp(): void
    {
        if (self::$_db === null) {
            // load demos config
            $initVars = get_defined_vars();
            $this->resetSuperglobalsFromRequest(new Request('GET', 'http://localhost/demos/'));

            /** @var App $app */
            require_once $this->demosDir . '/init-app.php';
            $initVars = array_diff_key(get_defined_vars(), $initVars + ['initVars' => true]);

            if (array_keys($initVars) !== ['app']) {
                throw new \atk4\ui\Exception('Demos init must setup only $app variable');
            }

            self::$_db = $app->db;

            // prevent $app to run on shutdown
            $app->run_called = true;
        }
    }

    public static function tearDownAfterClass(): void
    {
        self::$_db = null;
    }

    private function resetSuperglobalsFromRequest(RequestInterface $request): void
    {
        $_SERVER = [
            'REQUEST_URI' => (string) $request->getUri(),
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

        $_FILES = [];
        $_COOKIE = [];
        $_SESSION = [];

        \Closure::bind(function () {
            self::$_sentHeaders = [];
        }, null, App::class)();
    }

    private function createTestingApp(): App
    {
        $app = new class() extends App {
            protected function setupAlwaysRun(): void
            {
                // no register_shutdown_function
            }

            public function callExit($for_shutdown = false): void
            {
                // TODO is the shutdown hook called somewhere?

                throw new DemosTestExitException();
            }
        };
        $app->initLayout(\atk4\ui\Layout\Maestro::class);

        // clone DB (mainly because all Models remains attached now, TODO can be removed once they are GCed)
        $app->db = clone self::$_db;

        return $app;
    }

    protected function getClient(): Client
    {
        $handler = function (RequestInterface $request) {
            // emulate request
            $this->resetSuperglobalsFromRequest($request);
            $localPath = __DIR__ . '/../' . $request->getUri()->getPath();

            ob_start();

            try {
                $app = $this->createTestingApp();
                require $localPath;

                if (!$app->run_called) {
                    $app->run();
                }
            } catch (\Throwable $e) {
                // session_start() or ini_set() functions can be used only with native HTTP tests
                // override test expectation here to finish there tests cleanly (TODO better to make the code testable without calling these functions)
                if ($e instanceof \ErrorException && preg_match('~^(session_start|ini_set)\(\).* headers already sent$~', $e->getMessage())) {
                    $this->expectExceptionObject($e);
                }

                if (!($e instanceof DemosTestExitException)) {
                    throw $e;
                }
            } finally {
                $body = ob_get_contents();
                ob_end_clean();
            }

            [$statusCode, $headers] = \Closure::bind(function () {
                $statusCode = 200;
                $headers = self::$_sentHeaders;
                if (isset($headers[self::HEADER_STATUS_CODE])) {
                    $statusCode = $headers[self::HEADER_STATUS_CODE];
                    unset($headers[self::HEADER_STATUS_CODE]);
                }

                return [$statusCode, $headers];
            }, null, App::class)();

            // Attach a response to the easy handle with the parsed headers.
            $response = new \GuzzleHttp\Psr7\Response(
                $statusCode,
                $headers,
                \GuzzleHttp\Psr7\stream_for($body),
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
            return $this->getClient()->request(isset($options['form_params']) !== null ? 'POST' : 'GET', $this->getPathWithAppVars($path), $options);
        } catch (\GuzzleHttp\Exception\ServerException $ex) {
            $exFactoryWithFullBody = new class('', $ex->getRequest()) extends \GuzzleHttp\Exception\RequestException {
                public static function getResponseBodySummary(ResponseInterface $response)
                {
                    return $response->getBody()->getContents();
                }
            };

            throw $exFactoryWithFullBody->create($ex->getRequest(), $ex->getResponse());
        }
    }

    protected function getPathWithAppVars(string $path): string
    {
        return 'demos/' . $path;
    }

    protected $regexHtml = '~^..DOCTYPE~';
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
    protected $regexSse = '~^(id|event|data).*$~m';

    public function demoFilesProvider(): array
    {
        $excludeDirs = ['_demo-data', '_includes', '_unit-test', 'special'];
        $excludeFiles = ['layout/layouts_error.php'];

        $files = [];
        $files[] = ['index.php'];
        foreach (array_diff(scandir($this->demosDir), ['.', '..'], $excludeDirs) as $dir) {
            if (!is_dir($this->demosDir . '/' . $dir)) {
                continue;
            }

            foreach (scandir($this->demosDir . '/' . $dir) as $f) {
                if (substr($f, -4) !== '.php' || in_array($dir . '/' . $f, $excludeFiles, true)) {
                    continue;
                }

                $files[] = [$dir . '/' . $f];
            }

            break; // debug only!
        }

        return $files;
    }

    /**
     * @dataProvider demoFilesProvider
     */
    public function testDemosStatusAndHtmlResponse(string $uri): void
    {
        $response = $this->getResponseFromRequest($uri);
        $this->assertSame(200, $response->getStatusCode(), ' Status error on ' . $uri);
        $this->assertMatchesRegularExpression($this->regexHtml, $response->getBody()->getContents(), ' RegExp error on ' . $uri);
    }

    public function testResponseError(): void
    {
        if (static::class === self::class) { // TODO
            $this->assertTrue(true);

            return;
        }

        $this->expectExceptionCode(500);
        $this->getResponseFromRequest('layout/layouts_error.php');
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
    public function testDemoGet(string $uri): void
    {
        $response = $this->getResponseFromRequest($uri);
        $this->assertSame(200, $response->getStatusCode(), ' Status error on ' . $uri);
        $this->assertSame('text/html', preg_replace('~;\s*charset=.+$~', '', $response->getHeaderLine('Content-Type')), ' Content type error on ' . $uri);
        $this->assertMatchesRegularExpression($this->regexHtml, $response->getBody()->getContents(), ' RegExp error on ' . $uri);
    }

    public function testWizard(): void
    {
        $response = $this->getResponseFromRequest(
            'interactive/wizard.php?demo_wizard=1&w_form_submit=ajax&__atk_callback=1',
            ['form_params' => [
                'dsn' => 'mysql://root:root@db-host.example.com/atk4',
                'w_form_submit' => 'submit',
            ]]
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertMatchesRegularExpression($this->regexJson, $response->getBody()->getContents());

        $response = $this->getResponseFromRequest('interactive/wizard.php?atk_admin_wizard=2&name=Country');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertMatchesRegularExpression($this->regexHtml, $response->getBody()->getContents());
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
        $files[] = ['_unit-test/reload.php?c_reload=ajax&__atk_callback=1'];
        // test catch exceptions
        $files[] = ['_unit-test/exception.php?m_cb=ajax&__atk_callback=1&__atk_json=1'];
        $files[] = ['_unit-test/exception.php?m2_cb=ajax&__atk_callback=1&__atk_json=1'];

        return $files;
    }

    /**
     * @dataProvider jsonResponseProvider
     */
    public function testDemoAssertJsonResponse(string $uri): void
    {
        if (static::class === self::class) { // TODO
            $this->assertTrue(true);

            return;
        }

        $response = $this->getResponseFromRequest($uri);
        $this->assertSame(200, $response->getStatusCode(), ' Status error on ' . $uri);
        if (!($this instanceof DemosHttpNoExitTest)) { // content type is not set when App->call_exit equals to true
            $this->assertSame('application/json', preg_replace('~;\s*charset=.+$~', '', $response->getHeaderLine('Content-Type')), ' Content type error on ' . $uri);
        }
        $this->assertMatchesRegularExpression($this->regexJson, $response->getBody()->getContents(), ' RegExp error on ' . $uri);
    }

    /**
     * Test jsSSE and Console.
     */
    public function sseResponseProvider(): array
    {
        $files = [];
        $files[] = ['_unit-test/sse.php?see_test=ajax&__atk_callback=1&__atk_sse=1'];
        $files[] = ['_unit-test/console.php?console_test=ajax&__atk_callback=1&__atk_sse=1'];
        if (!($this instanceof DemosHttpNoExitTest)) { // ignore content type mismatch when App->call_exit equals to true
            $files[] = ['_unit-test/console_run.php?console_test=ajax&__atk_callback=1&__atk_sse=1'];
            $files[] = ['_unit-test/console_exec.php?console_test=ajax&__atk_callback=1&__atk_sse=1'];
        }

        return $files;
    }

    /**
     * @dataProvider sseResponseProvider
     */
    public function testDemoAssertSseResponse(string $uri): void
    {
        $response = $this->getResponseFromRequest($uri);
        $this->assertSame(200, $response->getStatusCode(), ' Status error on ' . $uri);

        $output_rows = preg_split('~\r?\n|\r~', $response->getBody()->getContents());

        $this->assertGreaterThan(0, count($output_rows), ' Response is empty on ' . $uri);

        // check SSE Syntax
        foreach ($output_rows as $index => $sse_line) {
            if (empty($sse_line)) {
                continue;
            }

            preg_match_all($this->regexSse, $sse_line, $matchesAll);
            $format_match_string = implode('', $matchesAll[0] ?? ['error']);

            $this->assertSame(
                $sse_line,
                $format_match_string,
                ' Testing SSE response line ' . $index . ' with content ' . $sse_line . ' on ' . $uri
            );
        }
    }

    public function jsonResponsePostProvider(): array
    {
        $files = [];
        $files[] = [
            '_unit-test/post.php?test_form_submit=ajax&__atk_callback=1',
            [
                'f1' => 'v1',
                'test_form_submit' => 'submit',
            ],
        ];

        // for jsNotify coverage
        $files[] = [
            'obsolete/notify2.php?notify_submit=ajax&__atk_callback=1',
            [
                'text' => 'This text will appear in notification',
                'icon' => 'warning sign',
                'color' => 'green',
                'transition' => 'jiggle',
                'width' => '25%',
                'position' => 'topRight',
                'attach' => 'Body',
                'notify_submit' => 'submit',
            ],
        ];

        return $files;
    }

    /**
     * @dataProvider jsonResponsePostProvider
     */
    public function testDemoAssertJsonResponsePost(string $uri, array $postData)
    {
        $response = $this->getResponseFromRequest($uri, ['form_params' => $postData]);
        $this->assertSame(200, $response->getStatusCode(), ' Status error on ' . $uri);
        $this->assertMatchesRegularExpression($this->regexJson, $response->getBody()->getContents(), ' RegExp error on ' . $uri);
    }
}

class DemosTestExitException extends \atk4\ui\Exception
{
}
