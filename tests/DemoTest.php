<?php

namespace atk4\ui\tests;

use atk4\core\Exception;
use atk4\ui\App;
use atk4\ui\Exception\ExitApplicationException;
use PHPUnit\Framework\TestCase;

/**
 * Making sure demo pages don't throw exceptions and coverage is
 * handled.
 */
class DemoTest extends TestCase
{
    public function setUp()
    {
        chdir('demos');
    }

    public function tearDown()
    {
        chdir('..');
    }

    public function inc($file, $get, $post): App
    {
        $app = null;

        $_SERVER['REQUEST_URI'] = '/ui/'.$file;

        $_GET = $get ?? [];
        $_POST = $post ?? [];

        include $file;

        return $app;
    }

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
     * @runInSeparateProcess
     * @dataProvider HTMLResponseDataProvider
     *
     * @param string     $page
     * @param array|null $get
     * @param array|null $post
     *
     * @throws Exception
     */
    public function testDemoHTMLResponse($page, ?array $get = null, ?array $post = null)
    {
        $this->expectOutputRegex($this->regexHTML);

        try {
            $app = $this->inc($page, $get, $post);
            if (!$app->run_called) {
                $app->run();
            }
        } catch (ExitApplicationException $e) {
        } catch (Exception $e) {
            $e->addMoreInfo('page', $page);
            $e->addMoreInfo('get', $get);
            $e->addMoreInfo('post', $post);

            throw $e;
        }

        // echo the page to where is the error
        // PCRE checks only the beginning of the output
        var_dump(
            [
                $page,
                $get,
                $post,
            ]
        );
    }

    public function HTMLResponseDataProvider()
    {
        $files = [];

        $files[] = ['accordion.php'];
        $files[] = ['accordion-nested.php'];
        $files[] = ['autocomplete.php'];
        $files[] = ['breadcrumb.php'];
        $files[] = ['button.php'];
        $files[] = ['card.php'];
        $files[] = ['checkbox.php'];
        $files[] = ['columns.php'];
        $files[] = ['console.php'];
        $files[] = ['crud.php'];
        $files[] = ['crud2.php'];
        $files[] = ['crud3.php'];
        $files[] = ['dropdown-plus.php'];
        $files[] = ['field.php'];
        $files[] = ['field2.php'];
        $files[] = ['form.php'];
        $files[] = ['form2.php'];
        $files[] = ['form3.php'];
        $files[] = ['form4.php'];
        $files[] = ['form5.php'];
        $files[] = ['form6.php'];
        $files[] = ['form-custom-layout.php'];
        $files[] = ['form-section.php'];
        $files[] = ['form-section-accordion.php'];
        $files[] = ['grid.php'];
        $files[] = ['grid-layout.php'];
        $files[] = ['header.php'];
        $files[] = ['index.php'];
        $files[] = ['init.php'];
        $files[] = ['js.php'];
        $files[] = ['jscondform.php'];
        $files[] = ['menu.php'];
        $files[] = ['message.php'];
        $files[] = ['modal.php'];
        $files[] = ['multitable.php'];
        $files[] = ['notify.php'];
        $files[] = ['notify2.php'];
        $files[] = ['paginator.php'];
        $files[] = ['progress.php'];
        $files[] = ['recursive.php'];
        $files[] = ['reloading.php'];
        $files[] = ['scroll-container.php'];
        $files[] = ['scroll-grid.php'];
        $files[] = ['scroll-grid-container.php'];
        $files[] = ['scroll-lister.php'];
        $files[] = ['scroll-table.php'];

        $files[] = ['sse.php'];

        $files[] = ['sticky.php'];
        $files[] = [
            'sticky.php',
            [
                'c' => 'OHO',
            ],
        ];

        $files[] = ['sticky2.php'];

        $files[] = ['table.php'];
        $files[] = ['table2.php'];
        $files[] = ['tablecolumnmenu.php'];
        $files[] = ['tablecolumns.php'];
        $files[] = ['tabs.php'];
        $files[] = ['toast.php'];
        $files[] = ['upload.php'];
        $files[] = ['view.php'];

        $files[] = ['virtual.php'];

        $files[] = [
            'virtual.php',
            [
                'atk_admin_virtualpage' => 'callback',
                '__atk_callback'        => 1,
            ],
        ];

        $files[] = ['wizard.php'];

        $files[] = [
            'wizard.php',
            [
                'atk_admin_wizard' => 1,
                '__atk_callback'   => 1,
            ],
        ];

        $files[] = [
            'wizard.php',
            [
                'atk_admin_wizard' => 2,
                '__atk_callback'   => 1,
            ],
        ];

        /* need session
        $files[]=['wizard.php', [
                'atk_admin_wizard'=>2,
                'name' => 'Country'
            ]
        ];
        */

        $files[] = [
            'wizard.php',
            [
                'atk_admin_wizard' => 3,
                '__atk_callback'   => 1,
            ],
        ];

        $files[] = [
            'wizard.php',
            [
                'atk_admin_wizard' => 4,
                '__atk_callback'   => 1,
            ],
        ];

        //$files[]='jssearch.php'; // this call Method Grid->addJsSearch which not exists

        $files[] = ['jssortable.php'];
        $files[] = ['label.php'];
        $files[] = ['layouts.php'];
        $files[] = ['layouts_admin.php'];

        //$files[]='layouts_error.php'; // intended to raise exception and display nicely
        //$files[]='layouts_manual.php'; // doesn't use $app and gives error in DemoTest->inc
        //$files[]='layouts_nolayout.php'; // doesn't use $app and gives error in DemoTest->inc

        $files[] = ['lister.php'];
        $files[] = ['lister-ipp.php'];
        $files[] = ['loader2.php'];
        $files[] = ['loader.php'];
        $files[] = ['modal2.php'];
        $files[] = ['popup.php'];
        $files[] = ['tablefilter.php'];
        $files[] = ['vue-component.php'];

        return $files;
    }

    /**
     * @runInSeparateProcess
     * @dataProvider layoutDataProvider
     *
     * @param string     $page
     * @param array|null $get
     * @param array|null $post
     *
     * @throws Exception
     */
    public function testDemoLayout($file)
    {
        $this->expectOutputRegex($this->regexHTML);
        include $file;
    }

    public function layoutDataProvider()
    {
        return [
            ['layouts_manual.php'],
        ];
    }

    /**
     * @dataProvider JSONResponseDataProvider
     *
     * @param string     $page
     * @param array|null $get
     * @param array|null $post
     *
     * @throws Exception
     */
    public function testDemoAssertJSONResponse($page, ?array $get = null, ?array $post = null)
    {
        $this->expectOutputRegex($this->regexJSON);

        try {
            $app = $this->inc($page, $get, $post);
            if (!$app->run_called) {
                $app->run();
            }
        } catch (ExitApplicationException $e) {
        } catch (Exception $e) {
            $e->addMoreInfo('page', $page);
            $e->addMoreInfo('get', $get);
            $e->addMoreInfo('post', $post);

            throw $e;
        }
    }

    public function JSONResponseDataProvider()
    {
        $files = [];

        $files[] = [
            'sticky2.php',
            [
                '__atk_reload' => 'atk_admin_button',
            ],
        ];

        $files[] = [
            'sticky2.php',
            [
                'atk_admin_loader_callback' => 'ajax',
                '__atk_callback'            => '1',
            ],
        ];

        $files[] = [
            'virtual.php',
            [
                'atk_admin_label_2_click' => 'ajax',
                '__atk_callback'          => '1',
            ],
        ];

        $files[] = [
            'wizard.php',
            [
                'atk_admin_wizard'             => 1,
                'atk_admin_wizard_form_submit' => 'ajax',
                '__atk_callback'               => 1,
            ],
            [
                'dsn'                          => 'mysql://root:root@localhost/atk4',
                'atk_admin_wizard_form_submit' => 'submit',
            ],
        ];

        return $files;
    }

    /**
     * @dataProvider SSEResponseDataProvider
     *
     * @param string     $page
     * @param array|null $get
     * @param array|null $post
     *
     * @throws Exception
     */
    public function testDemoAssertSSEResponse($page, ?array $get = null, ?array $post = null)
    {
        ob_start();

        $exit_correctly = false;

        try {
            $app = $this->inc($page, $get, $post);
            $app->run();
        } catch (ExitApplicationException $e) {
            $exit_correctly = true;
        } catch (\Throwable $t) {
            $test = true;
        }

        $output_rows = explode(PHP_EOL, ob_get_clean());

        // SSE is grredy on flushing buffers,
        // sometimes it will close buffer of PHPUnit giving an error
        if (ob_get_level() === 0) {
            ob_start();
        }

        // check if SSE exit from application correctly
        $this->assertTrue($exit_correctly);

        $this->assertGreaterThan(0, count($output_rows));

        // check SSE Syntax
        foreach ($output_rows as $line) {
            if (empty($line) || $line === null) {
                continue;
            }

            preg_match('/^[id|event|data].*$/', $line, $matches);
            $this->assertEquals($line, $matches[0]);
        }
    }

    public function SSEResponseDataProvider()
    {
        $files = [];

        $files[] = [
            'sse.php',
            [
                'atk_admin_jssse' => 'ajax',
                '__atk_callback'  => '1',
                'event'           => 'sse',
            ],
        ];

        /*
        // Cannot be done need previous steps
        // and session opened
        $files[]=['wizard.php', [
                'atk_admin_wizard'=>3,
                'atk_admin_wizard_console_jssse'=>'ajax',
                '__atk_callback'=>1,
                'event'=>'sse'
            ]
        ];
        */

        $files[] = [
            'console.php',
            [
                'atk_admin_tabs_tabssubview_console_jssse' => 'ajax',
                '__atk_callback'                           => '1',
                'event'                                    => 'sse',
            ],
        ];

        $files[] = [
            'console.php',
            [
                'atk_admin_tabs_tabssubview_2_virtualpage'               => 'cut',
                'atk_admin_tabs_tabssubview_2_virtualpage_console_jssse' => 'ajax',
                '__atk_callback'                                         => 1,
                'event'                                                  => 'sse',
            ],
        ];

        $files[] = [
            'console.php',
            [
                'atk_admin_tabs_tabssubview_3_virtualpage'               => 'cut',
                'atk_admin_tabs_tabssubview_3_virtualpage_console_jssse' => 'ajax',
                '__atk_callback'                                         => 1,
                'event'                                                  => 'sse',
            ],
        ];

        return $files;
    }
}
