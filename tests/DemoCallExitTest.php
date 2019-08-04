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

    public function testableDemoFilesdataProvider()
    {
        $files = [];
        foreach (scandir(getcwd().DIRECTORY_SEPARATOR.'demos') as $file) {
            if (is_dir($file) || substr($file, -3) !== 'php') {
                continue;
            }

            switch ($file) {
                case 'Session.php': // exclude - is a setup file
                case 'database.php': // exclude - is a setup file
                case 'db.example.php': // exclude - is a setup file
                case 'db.php': // exclude - is a setup file
                case 'db.travis.php': // exclude - is a setup file
                case 'somedatadef.php': // exclude - is a setup file
                case 'layouts_nolayout.php': // exclude - output only a partial html
                    continue 2;
                    break;
            }

            $files[] = [$file];
        }

        return $files;
    }

    /**
     * @dataProvider testableDemoFilesdataProvider
     *
     * @param string $uri
     */
    public function testDemoHTMLStatusAndResponse(string $uri)
    {
        $response = $this->getResponseFromRequestGET($uri);
        $this->assertEquals(200, $response->getStatusCode(), ' Status error on '.$uri);
        $this->assertRegExp($this->regexHTML, $response->getBody()->getContents(), ' RegExp error on '.$uri);
    }

    /**
     * @dataProvider casesDemoGETDataProvider
     *
     * @param string $uri
     */
    public function testDemoGet(string $uri)
    {
        $response = $this->getResponseFromRequestGET($uri);
        $this->assertEquals(200, $response->getStatusCode(), ' Status error on '.$uri);
        $this->assertRegExp($this->regexHTML, $response->getBody()->getContents(), ' RegExp error on '.$uri);
    }

    public function casesDemoGETDataProvider()
    {
        $files = [];
        $files[] = ['sticky.php?xx=YEY'];
        $files[] = ['sticky.php?c=OHO'];
        $files[] = ['sticky.php?xx=YEY&c=OHO'];

        return $files;
    }

    public function testWizard()
    {
        $response = $this->getResponseFromRequestFormPOST(
            'wizard.php?atk_admin_wizard=1&atk_admin_wizard_form_submit=ajax&__atk_callback=1',
            [
                'dsn'                          => 'mysql://root:root@db-host.example.com/atk4',
                'atk_admin_wizard_form_submit' => 'submit',
            ]
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertRegExp($this->regexJSON, $response->getBody()->getContents());

        $response = $this->getResponseFromRequestGET('wizard.php?atk_admin_wizard=2&name=Country');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertRegExp($this->regexHTML, $response->getBody()->getContents());
    }

    /**
     * @runInSeparateProcess
     * @dataProvider JSONResponseDataProvider
     *
     * @param string $uri
     *
     * @throws Exception
     */
    public function testDemoAssertJSONResponse(string $uri)
    {
        $response = $this->getResponseFromRequestGET($uri);
        $this->assertEquals(200, $response->getStatusCode(), ' Status error on '.$uri);
        $this->assertRegExp($this->regexJSON, $response->getBody()->getContents(), ' RegExp error on '.$uri);
    }

    public function JSONResponseDataProvider()
    {
        $files = [];
        $files[] = ['sticky2.php?__atk_reload=atk_admin_button'];
        $files[] = ['sticky2.php?atk_admin_loader_callback=ajax&__atk_callback1'];
        $files[] = ['virtual.php?atk_admin_label_2_click=ajax&__atk_callback=1'];

        return $files;
    }

    /**
     * @dataProvider SSEResponseDataProvider
     *
     * @param string $uri
     */
    public function testDemoAssertSSEResponse(string $uri)
    {
        $response = $this->getResponseFromRequestGET($uri);
        $this->assertEquals(200, $response->getStatusCode(), ' Status error on '.$uri);

        $output_rows = explode(PHP_EOL, $response->getBody()->getContents());

        $this->assertGreaterThan(0, count($output_rows), ' Response is empty on '.$uri);
        // check SSE Syntax
        foreach ($output_rows as $index => $sse_line) {
            if (empty($sse_line) || $sse_line === null) {
                continue;
            }

            preg_match('/^[id|event|data].*$/', $sse_line, $matches);
            $this->assertEquals(
                $sse_line, $matches[0] ?? 'error',
                ' Testing SSE response line '.$index.' with content '.$sse_line.' on '.$uri
            );
        }
    }

    public function SSEResponseDataProvider()
    {
        $files = [];
        $files[] = ['sse.php?atk_admin_jssse=ajax&__atk_callback=1&event=sse'];
        $files[] = ['console.php?atk_admin_tabs_tabssubview_console_jssse=ajax&__atk_callback=1&event=sse'];
        $files[] = ['console.php?atk_admin_tabs_tabssubview_2_virtualpage=cut&atk_admin_tabs_tabssubview_2_virtualpage_console_jssse=ajax&__atk_callback=1&event=sse'];
        $files[] = ['console.php?atk_admin_tabs_tabssubview_3_virtualpage=cut&atk_admin_tabs_tabssubview_3_virtualpage_console_jssse=ajax&__atk_callback=1&event=sse'];

        return $files;
    }
}
