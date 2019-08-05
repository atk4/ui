<?php

namespace atk4\ui\tests;

use atk4\core\Exception;
use atk4\ui\App;

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

    /**
     * No test done here.
     * Only populate data in model \File
     * if not it will trigger error later for record not found.
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $old_cwd = getcwd();
        chdir(getcwd().DIRECTORY_SEPARATOR.'./demos');
        $app = new App();
        $app->initLayout('Generic');

        require 'database.php';

        $file = new \File($app->db);
        $file->importFromFilesystem(getcwd());
        chdir($old_cwd);
    }

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
                case 'coverage.php': // exclude - is the coverage file
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
        $files[] = ['actions.php?__atk_m=atk_admin_crud_modal&atk_admin_crud_modal_view_callbacklater=ajax&__atk_callback=1&atk_admin_crud_view_table_actions=1&atk_admin_crud_view_view_paginator=1&json=true'];
        $files[] = ['actions.php?atk_admin_crud_edit=cut&__atk_callback=1&atk_admin_crud=1&atk_admin_crud_sort=&json=true'];
        $files[] = ['notify.php?__atk_m=atk_admin_modal&atk_admin_modal_view_callbacklater=ajax&__atk_callback=1&json=true'];
        $files[] = ['scroll-lister.php?atk_admin_view_2_view_lister_jspaginator=ajax&__atk_callback=1&page=2'];

        // test catch exceptions
        $files[] = ['exception_test.php?__atk_m=atk_admin_modal&atk_admin_modal_view_callbacklater=ajax&__atk_callback=1&json=true'];
        $files[] = ['exception_test.php?__atk_m=atk_admin_modal_2&atk_admin_modal_2_view_callbacklater=ajax&__atk_callback=1&json=true'];

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

    /**
     * @dataProvider JSONResponsePOSTDataProvider
     *
     * @param string $uri
     * @param array  $post_data
     */
    public function testDemoAssertJSONResponsePOST(string $uri, array $post_data)
    {
        $response = $this->getResponseFromRequestFormPOST($uri, $post_data);
        $this->assertEquals(200, $response->getStatusCode(), ' Status error on '.$uri);
        $this->assertRegExp($this->regexJSON, $response->getBody()->getContents(), ' RegExp error on '.$uri);
    }

    public function JSONResponsePOSTDataProvider()
    {
        $files = [];

        // IMPORT FROM FILESYSTEM
        $files[] = [
            'actions.php?atk_admin_gridlayout_basic_button_click=ajax&__atk_callback=1',
            [],
        ]; // btn confirm

        $files[] = [
            'actions.php?atk_admin_gridlayout_argumentform_form_submit=ajax&__atk_callback=1',
            [
                'path'                                          => '.',
                'atk_admin_gridlayout_argumentform_form_submit' => 'submit',
            ],
        ]; // btn run
        //
        $files[] = [
            'actions.php?atk_admin_gridlayout_preview_button_click=ajax&__atk_callback=1',
            [],
        ]; // btn confirm (console)

        $files[] = [
            'actions.php?atk_admin_crud_view_view_paginator=1&__atk_m=atk_admin_crud_modal&atk_admin_crud_modal_view_callbacklater=ajax&atk_admin_crud_modal_view_basic_button_click=ajax&atk_admin_crud_view_table_actions=1&__atk_callback=1',
            [

            ],
        ];

        // Grid buttons
        $files[] = [
            'actions.php?atk_admin_crud_edit=cut&atk_admin_crud_edit_form_submit=ajax&atk_admin_crud=1&__atk_callback=1',
            [
                'name'                            => 'index.php',
                'type'                            => 'php',
                'parent_folder_id'                => '1',
                'atk_admin_crud_edit_form_submit' => 'submit',
            ],
        ]; // edit

        $files[] = [
            'actions.php?atk_admin_crud_view_view_paginator=1&__atk_m=atk_admin_crud_modal&atk_admin_crud_modal_view_callbacklater=ajax&atk_admin_crud_modal_view_basic_button_click=ajax&atk_admin_crud_view_table_actions=1&__atk_callback=1',
            [],
        ]; // download : confirm

        // JS ACTIONS
        $files[] = [
            'jsactions.php?atk_admin_button_2_jscallback=ajax&__atk_callback=1',
            [
                'atk_event_id' => '',
                'path'         => '.',
            ],
        ];

        $files[] = [
            'jsactions.php?atk_admin_card_view_view_button_jscallback=ajax&__atk_callback=1',
            [
                'atk_event_id' => '',
            ],
        ];

        $files[] = [
            'notify.php?__atk_m=atk_admin_modal&atk_admin_modal_view_callbacklater=ajax&atk_admin_modal_view_form_submit=ajax&__atk_callback=1',
            [
                'name'                             => 'test',
                'atk_admin_modal_view_form_submit' => 'submit',
            ],
        ];

        $files[] = [
            'notify2.php?atk_admin_form_submit=ajax&__atk_callback=1',
            [
                'text'                  => 'This text will appear in notification',
                'icon'                  => 'warning sign',
                'color'                 => 'green',
                'transition'            => 'jiggle',
                'width'                 => '25%',
                'position'              => 'topRight',
                'attach'                => 'Body',
                'atk_admin_form_submit' => 'submit',
            ],
        ];

        $files[] = [
            'tablefilter.php?atk_admin_filterpopup_5_form_submit=ajax&__atk_callback=1',
            [
                'op'                                  => '=',
                'value'                               => '374',
                'range'                               => '',
                'atk_admin_filterpopup_5_form_submit' => 'submit',
            ],
        ];

        $files[] = [
            'tablefilter.php?atk_admin_filterpopup_4_form_submit=ajax&__atk_callback=1',
            [
                'op'                                  => 'between',
                'value'                               => '10',
                'range'                               => '20',
                'atk_admin_filterpopup_5_form_submit' => 'submit',
            ],
        ];

        return $files;
    }
}
