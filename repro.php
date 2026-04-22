<?php

declare(strict_types=1);

namespace Atk4\Ui\Repro;

use Atk4\Ui\Tests\DemosHttpTest;

require_once __DIR__ . '/vendor/autoload.php';

class Repro
{
    private DemosHttpTest $tc;

    public function setupWebserver(): void
    {
        DemosHttpTest::setUpBeforeClass();
        $this->tc = new DemosHttpTest('x');
        $tc = $this->tc;
        \Closure::bind(static fn () => $tc->setUp(), null, DemosHttpTest::class)();
    }

    public function curl(string $path, ?array $post = null): array
    {
        return $this->tc->curl('http://localhost:9687/' . $path, $post);
    }

    public function test(): void
    {
        $tests = [
            array(
                'demos/layout/layouts_error.php?APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                [],
            ), array(
                'demos/others/sticky.php?xx=YEY&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                [],
            ), array(
                'demos/others/sticky.php?c=OHO&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                [],
            ), array(
                'demos/others/sticky.php?xx=YEY&c=OHO&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                [],
            ), array(
                'demos/_unit-test/stream.php?size_mb=40&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                [],
            ), array(
                'demos/_unit-test/reload.php?__atk_reload=reload&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                [],
            ), array(
                'demos/_unit-test/reload.php?__atk_cb_c_reload=ajax&__atk_cbtarget=c_reload&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                [],
            ), array(
                'demos/_unit-test/exception.php?__atk_cb_m_cb=ajax&__atk_cbtarget=m_cb&__atk_json=1&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                [],
            ), array(
                'demos/_unit-test/exception.php?__atk_cb_m2_cb=ajax&__atk_cbtarget=m2_cb&__atk_json=1&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                [],
            ), array(
                'demos/_unit-test/sse.php?__atk_cb_see_test=ajax&__atk_cbtarget=1&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                [],
            ), array(
                'demos/_unit-test/console.php?__atk_cb_console_test=ajax&__atk_cbtarget=1&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                [],
            ), array(
                'demos/_unit-test/console_run.php?__atk_cb_console_test=ajax&__atk_cbtarget=1&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                [],
            ), array(
                'demos/_unit-test/console_exec.php?__atk_cb_console_test=ajax&__atk_cbtarget=1&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                [],
            ), array(
                'demos/_unit-test/post.php?__atk_cb_test_submit=ajax&__atk_cbtarget=test_submit&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                1 =>
                array(
                    'form_params' =>
                    array(
                        'f1' => 'v1',
                    ),
                ),
            ), array(
                'demos/_unit-test/callback-nested.php?err_sub_loader&__atk_cb_trigger_main_loader=callback&__atk_cbtarget=non_existing_target&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                [],
            ), array(
                'demos/_unit-test/callback-nested.php?__atk_cb_trigger_main_loader=callback&__atk_cb_trigger_sub_loader=callback&__atk_cbtarget=non_existing_target&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                [],
            ), array(
                'demos/_unit-test/callback-nested.php?err_main_loader&__atk_cb_trigger_main_loader=callback&__atk_cbtarget=trigger_main_loader&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                [],
            ), array(
                'demos/_unit-test/callback-nested.php?err_sub_loader&__atk_cb_trigger_main_loader=callback&__atk_cb_trigger_sub_loader=callback&__atk_cbtarget=trigger_sub_loader&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                [],
            ), array(
                'demos/_unit-test/callback-nested.php?err_sub_loader2&__atk_cb_trigger_main_loader=callback&__atk_cb_trigger_sub_loader=callback&__atk_cbtarget=trigger_sub_loader&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                [],
            ), array(
                'demos/_unit-test/post.php?__atk_cb_test_submit=ajax&__atk_cbtarget=test_submit&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                1 =>
                array(
                    'form_params' => [],
                ),
            ), array(
                'demos/_unit-test/fatal-error.php?type=oom&APP_CALL_EXIT=1&APP_CATCH_EXCEPTIONS=1',
                [],
            )
        ];

        foreach ($tests as $test) {
            [$code, $result] = $this->curl($test[0], $test[1]);
        }

        var_dump($code);
        // var_dump($result);
        $log = file_get_contents(__DIR__ . '/log');
        var_dump($log);

        if (str_contains($log, 'string(1) "y"')) {
            exit(1);
        }
    }
}

$tc = new Repro();
$tc->setupWebserver();
$tc->test();
