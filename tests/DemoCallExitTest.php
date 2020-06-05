<?php

namespace atk4\ui\tests;

use atk4\core\Exception;

/**
 * Making sure demo pages don't throw exceptions and coverage is
 * handled.
 */
class DemoCallExitTest extends BuiltInWebServerAbstract
{
    private $regexHTML = '/^..DOCTYPE/';
    private $regexJSON = '
  /
  (?(DEFINE)
     (?<number>   -? (?= [1-9]|0(?!\d) ) \d+ (\.\d+)? ([eE] [+-]? \d+)? )
     (?<boolean>   true | false | null )
     (?<string>    " ([^"\\\\]* | \\\\ ["\\\\bfnrt\/] | \\\\ u [0-9a-f]{4} )* " )
     (?<array>     \[  (?:  (?&json)  (?: , (?&json)  )*  )?  \s* \] )
     (?<pair>      \s* (?&string) \s* : (?&json)  )
     (?<object>    \{  (?:  (?&pair)  (?: , (?&pair)  )*  )?  \s* \} )
     (?<json>   \s* (?: (?&number) | (?&boolean) | (?&string) | (?&array) | (?&object) ) \s* )
  )
  \A (?&json) \Z
  /six
';

    private $regexSSE = '/^[data|id|event].*$/m';

    public function casesDemoFilesdataProvider()
    {
        // set demo directory that need to be scanned.
        $directories = [
            'basic',
            'collection',
            'form',
            'input',
            'interactive',
            'javascript',
            'layout',
            'others',
        ];

        // File that need to be exclude.
        $excludes = [
            'layouts_nolayout.php',
            'layouts_error.php',
        ];

        $files = [];
        $base_path = dirname(__DIR__) . '/demos';
        foreach ($directories as $dir) {
            $dir_path = $base_path . '/' . $dir;

            foreach (scandir($dir_path) as $f) {
                if (substr($f, -4) !== '.php' || is_dir($f) || in_array($f, $excludes, true)) {
                    continue;
                }

                $files[] = [$dir . '/' . $f];
            }
        }

        // add index.
        $files[] = ['index.php'];

        return $files;
    }

    /**
     * @dataProvider casesDemoFilesdataProvider
     */
    public function testDemoHTMLStatusAndResponse(string $uri)
    {
        $response = $this->getResponseFromRequest($uri);
        $this->assertSame(200, $response->getStatusCode(), ' Status error on ' . $uri);
        $this->assertMatchesRegularExpression($this->regexHTML, $response->getBody()->getContents(), ' RegExp error on ' . $uri);
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
        $this->assertMatchesRegularExpression($this->regexHTML, $response->getBody()->getContents(), ' RegExp error on ' . $uri);
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
        $this->assertMatchesRegularExpression($this->regexJSON, $response->getBody()->getContents());

        $response = $this->getResponseFromRequest('interactive/wizard.php?atk_admin_wizard=2&name=Country');
        $this->assertSame(200, $response->getStatusCode());
        $this->assertMatchesRegularExpression($this->regexHTML, $response->getBody()->getContents());
    }

    /**
     * @dataProvider JSONResponseDataProvider
     *
     * @throws Exception
     */
    public function testDemoAssertJSONResponse(string $uri)
    {
        $response = $this->getResponseFromRequest($uri);
        $this->assertSame(200, $response->getStatusCode(), ' Status error on ' . $uri);
        if (!($this instanceof DemoCallExitExceptionTest)) { // content type is not set when App->call_exit equals to true
            $this->assertSame('application/json', preg_replace('~;\s*charset=.+$~', '', $response->getHeaderLine('Content-Type')), ' Content type error on ' . $uri);
        }
        $this->assertMatchesRegularExpression($this->regexJSON, $response->getBody()->getContents(), ' RegExp error on ' . $uri);
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

            preg_match_all('/^(id|event|data).*$/m', $sse_line, $matches);

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
        if (!($this instanceof DemoCallExitExceptionTest)) { // ignore content type mismatch when App->call_exit equals to true
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
        $this->assertMatchesRegularExpression($this->regexJSON, $response->getBody()->getContents(), ' RegExp error on ' . $uri);
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

        return $files;
    }
}
