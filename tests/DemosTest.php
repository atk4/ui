<?php

declare(strict_types=1);

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;
use Psr\Http\Message\ResponseInterface;

/**
 * @group debugdebug
 */
abstract class DemosTest extends AtkPhpunit\TestCase
{
    /** @var string */
    protected $demosDir = __DIR__ . '/../demos';

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

    protected function getPathWithAppVars($path)
    {
        return self::$webserver_root . $path; // TODO do we need a basepatch then?
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

    /**
     * Test all demos/files.
     */
    public function casesDemoFilesdataProvider(): array
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
        }

        return $files;
    }

    /**
     * @dataProvider casesDemoFilesdataProvider
     */
    public function testDemoHTMLStatusAndResponse(string $uri)
    {
        $response = $this->getResponseFromRequest($uri);
        $this->assertSame(200, $response->getStatusCode(), ' Status error on ' . $uri);
        $this->assertMatchesRegularExpression($this->regexHtml, $response->getBody()->getContents(), ' RegExp error on ' . $uri);
    }

    public function testResponseError()
    {
        $this->expectExceptionCode(500);
        $this->getResponseFromRequest('layout/layouts_error.php');
    }

    /**
     * @dataProvider casesDemoGETDataProvider
     */
    public function testDemoGet(string $uri)
    {
        $response = $this->getResponseFromRequest($uri);
        $this->assertSame(200, $response->getStatusCode(), ' Status error on ' . $uri);
        $this->assertSame('text/html', preg_replace('~;\s*charset=.+$~', '', $response->getHeaderLine('Content-Type')), ' Content type error on ' . $uri);
        $this->assertMatchesRegularExpression($this->regexHtml, $response->getBody()->getContents(), ' RegExp error on ' . $uri);
    }

    public function casesDemoGETDataProvider()
    {
        $files = [];
        $files[] = ['others/sticky.php?xx=YEY'];
        $files[] = ['others/sticky.php?c=OHO'];
        $files[] = ['others/sticky.php?xx=YEY&c=OHO'];

        return $files;
    }

    public function testWizard()
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
     * @dataProvider JSONResponseDataProvider
     */
    public function testDemoAssertJSONResponse(string $uri)
    {
        $response = $this->getResponseFromRequest($uri);
        $this->assertSame(200, $response->getStatusCode(), ' Status error on ' . $uri);
        if (!($this instanceof DemosHttpNoExitTest)) { // content type is not set when App->call_exit equals to true
            $this->assertSame('application/json', preg_replace('~;\s*charset=.+$~', '', $response->getHeaderLine('Content-Type')), ' Content type error on ' . $uri);
        }
        $this->assertMatchesRegularExpression($this->regexJson, $response->getBody()->getContents(), ' RegExp error on ' . $uri);
    }

    /**
     * Test reload and loader callback.
     *
     * @return array
     */
    public function JSONResponseDataProvider()
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
     * @dataProvider SSEResponseDataProvider
     */
    public function testDemoAssertSSEResponse(string $uri)
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

            $matches = [];

            preg_match_all($this->regexSse, $sse_line, $matches);

            $format_match_string = implode('', $matches[0] ?? ['error']);

            $this->assertSame(
                $sse_line,
                $format_match_string,
                ' Testing SSE response line ' . $index . ' with content ' . $sse_line . ' on ' . $uri
            );
        }
    }

    /**
     * Test jsSSE and Console.
     *
     * @return array
     */
    public function SSEResponseDataProvider()
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
     * @dataProvider JSONResponsePOSTDataProvider
     */
    public function testDemoAssertJSONResponsePOST(string $uri, array $postData)
    {
        $response = $this->getResponseFromRequest($uri, ['form_params' => $postData]);
        $this->assertSame(200, $response->getStatusCode(), ' Status error on ' . $uri);
        $this->assertMatchesRegularExpression($this->regexJson, $response->getBody()->getContents(), ' RegExp error on ' . $uri);
    }

    public function JSONResponsePOSTDataProvider()
    {
        $files = [];
        $files[] = [
            '_unit-test/post.php?test_form_submit=ajax&__atk_callback=1',
            [
                'f1' => 'v1',
                'test_form_submit' => 'submit',
            ],
        ];

        // Getting back jsNotify coverage.
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
}
