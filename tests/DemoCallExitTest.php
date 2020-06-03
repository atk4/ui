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
        $response = $this->getResponseFromRequestGET($uri);
        $this->assertSame(200, $response->getStatusCode(), ' Status error on ' . $uri);
        $this->assertMatchesRegularExpression($this->regexHTML, $response->getBody()->getContents(), ' RegExp error on ' . $uri);
    }

    public function testResponseError()
    {
        $this->expectExceptionCode(500);
        $this->getResponseFromRequestGET('layout/layouts_error.php');
    }

    /**
     * @dataProvider casesDemoGETDataProvider
     */
    public function testDemoGet(string $uri)
    {
        $response = $this->getResponseFromRequestGET($uri);
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
        $response = $this->getResponseFromRequestFormPOST(
            'interactive/wizard.php?atk_admin_wizard=1&atk_admin_wizard_form_submit=ajax&__atk_callback=1',
            [
                'dsn' => 'mysql://root:root@db-host.example.com/atk4',
                'atk_admin_wizard_form_submit' => 'submit',
            ]
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertMatchesRegularExpression($this->regexJSON, $response->getBody()->getContents());

        $response = $this->getResponseFromRequestGET('interactive/wizard.php?atk_admin_wizard=2&name=Country');
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
        $response = $this->getResponseFromRequestGET($uri);
        $this->assertSame(200, $response->getStatusCode(), ' Status error on ' . $uri);
        if (!($this instanceof DemoCallExitExceptionTest)) { // content type is not set when App->call_exit equals to true
            $this->assertSame('application/json', preg_replace('~;\s*charset=.+$~', '', $response->getHeaderLine('Content-Type')), ' Content type error on ' . $uri);
        }
        $this->assertMatchesRegularExpression($this->regexJSON, $response->getBody()->getContents(), ' RegExp error on ' . $uri);
    }

    public function JSONResponseDataProvider()
    {
        $files = [];
        $files[] = ['others/sticky2.php?__atk_reload=atk_admin_button'];
        $files[] = ['others/sticky2.php?atk_admin_loader_callback=ajax&__atk_callback1'];
        $files[] = ['collection/actions.php?atk_admin_gridlayout_basic_button_click=ajax&__atk_callback=1']; // need to call this before calls other actions to fill model files
        $files[] = ['collection/actions.php?atk_useraction_file_edit_loader_callback=ajax&__atk_callback=1&atk_useraction_file_edit=1&step=fields'];
        $files[] = ['obsolete/notify.php?__atk_m=atk_admin_modal&atk_admin_modal_view_callbacklater=ajax&__atk_callback=1&__atk_json=1'];
        $files[] = ['interactive/scroll-lister.php?atk_admin_view_2_view_lister_jspaginator=ajax&__atk_callback=1&page=2'];

        // test catch exceptions
        $files[] = ['_unit-test/exception_test.php?__atk_m=atk_admin_modal&atk_admin_modal_view_callbacklater=ajax&__atk_callback=1&__atk_json=1'];
        $files[] = ['_unit-test/exception_test.php?__atk_m=atk_admin_modal_2&atk_admin_modal_2_view_callbacklater=ajax&__atk_callback=1&__atk_json=1'];

        return $files;
    }

    /**
     * @dataProvider SSEResponseDataProvider
     */
    public function testDemoAssertSSEResponse(string $uri)
    {
        $response = $this->getResponseFromRequestGET($uri);
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

    public function SSEResponseDataProvider()
    {
        $files = [];
        $files[] = ['interactive/sse.php?atk_admin_jssse=ajax&__atk_callback=1&__atk_sse=1'];
        $files[] = ['interactive/console.php?atk_admin_tabs_tabssubview_console_jssse=ajax&__atk_callback=1&__atk_sse=1'];
        if (!($this instanceof DemoCallExitExceptionTest)) { // ignore content type mismatch when App->call_exit equals to true
            $files[] = ['interactive/console.php?atk_admin_tabs_tabssubview_2_virtualpage=cut&atk_admin_tabs_tabssubview_2_virtualpage_console_jssse=ajax&__atk_callback=1&__atk_sse=1'];
            $files[] = ['interactive/console.php?atk_admin_tabs_tabssubview_3_virtualpage=cut&atk_admin_tabs_tabssubview_3_virtualpage_console_jssse=ajax&__atk_callback=1&__atk_sse=1'];
        }

        return $files;
    }

    /**
     * @dataProvider JSONResponsePOSTDataProvider
     */
    public function testDemoAssertJSONResponsePOST(string $uri, array $post_data)
    {
        $response = $this->getResponseFromRequestFormPOST($uri, $post_data);
        $this->assertSame(200, $response->getStatusCode(), ' Status error on ' . $uri);
        $this->assertMatchesRegularExpression($this->regexJSON, $response->getBody()->getContents(), ' RegExp error on ' . $uri);
    }

    public function JSONResponsePOSTDataProvider()
    {
        $files = [];

        // IMPORT FROM FILESYSTEM
        // this is needed to populate grid for later calls to row actions
        $files[] = [
            'collection/actions.php?atk_admin_gridlayout_basic_button_click=ajax&__atk_callback=1',
            [],
        ]; // btn confirm

        $files[] = [
            'collection/actions.php?atk_admin_gridlayout_argumentform_form_submit=ajax&__atk_callback=1',
            [
                'path' => '.',
                'atk_admin_gridlayout_argumentform_form_submit' => 'submit',
            ],
        ]; // btn run
        //
        $files[] = [
            'collection/actions.php?atk_admin_gridlayout_preview_button_click=ajax&__atk_callback=1',
            [],
        ]; // btn confirm (console)
        // Lines below gives error on Travis
        // Error is clear "Exception : record not found"
        // like the Model Files is not imported and there no records in table to be loaded
        // But few lines above i make the import and if run locally it works perfect
        /*
        // Grid buttons
        $files[] = [
            '/collection/actions.php?atk_admin_crud_edit=cut&atk_admin_crud_edit_form_submit=ajax&atk_admin_crud=1&__atk_callback=1',
            [
                'name'                            => 'index.php',
                'type'                            => 'php',
                'parent_folder_id'                => '1',
                'atk_admin_crud_edit_form_submit' => 'submit',
            ],
        ]; // edit

        $files[] = [
            '/collection/actions.php?atk_admin_crud_view_view_paginator=1&__atk_m=atk_admin_crud_modal&atk_admin_crud_modal_view_callbacklater=ajax&atk_admin_crud_modal_view_basic_button_click=ajax&atk_admin_crud_view_table_actions=1&__atk_callback=1',
            [],
        ]; // download : confirm
        */
        // JS ACTIONS
        $files[] = [
            'collection/jsactions.php?atk_admin_jsuseraction=ajax&__atk_callback=1',
            [
                'path' => '.',
            ],
        ];

        $files[] = [
            'collection/jsactions.php?atk_admin_card_view_view_button_jsuseraction=ajax&__atk_callback=1',
            [
            ],
        ];

        $files[] = [
            'obsolete/notify.php?__atk_m=atk_admin_modal&atk_admin_modal_view_callbacklater=ajax&atk_admin_modal_view_form_submit=ajax&__atk_callback=1',
            [
                'name' => 'test',
                'atk_admin_modal_view_form_submit' => 'submit',
            ],
        ];

        $files[] = [
            'obsolete/notify2.php?atk_admin_form_submit=ajax&__atk_callback=1',
            [
                'text' => 'This text will appear in notification',
                'icon' => 'warning sign',
                'color' => 'green',
                'transition' => 'jiggle',
                'width' => '25%',
                'position' => 'topRight',
                'attach' => 'Body',
                'atk_admin_form_submit' => 'submit',
            ],
        ];

        $files[] = [
            'collection/tablefilter.php?atk_admin_view_grid_view_filterpopup_5_form_submit=ajax&__atk_callback=1',
            [
                'op' => '=',
                'value' => '374',
                'range' => '',
                'atk_admin_view_grid_view_filterpopup_5_form_submit' => 'submit',
            ],
        ];

        $files[] = [
            'collection/tablefilter.php?atk_admin_view_grid_view_filterpopup_4_form_submit=ajax&__atk_callback=1',
            [
                'op' => 'between',
                'value' => '10',
                'range' => '20',
                'atk_admin_view_grid_view_filterpopup_4_form_submit' => 'submit',
            ],
        ];

        return $files;
    }
}
