<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\App;
use Atk4\Ui\Exception\ExitApplicationError;
use Atk4\Ui\Exception\LateOutputError;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\Layout;

class AppPsr7Test extends TestCase
{
    protected function getApp(): App
    {
        $app = new class(['call_exit' => false, 'catch_exceptions' => false, 'always_run' => false]) extends App {

            protected function emitResponse(): void
            {
                // no emitting to allow fast unit test
            }
        };
        $app->initLayout([Layout::class]);

        return $app;
    }

    public function testResponseHeaders(): void
    {
        $app = $this->getApp();
        $app->setResponseHeader('heAder-test', 'value1');
        $app->setResponseHeader('Header-Test', 'value2');

        try {
            $app->terminateHtml('', [
                'Header-Test' => 'value3'
            ]);
        } catch (ExitApplicationError $e) {

        }

        $this->assertSame('value3', $app->getResponse()->getHeaderLine('header-test'));
    }

    public function testResponseHeaders2(): void
    {
        $app = $this->getApp();
        $app->setResponseHeader('heAder-test', 'value1');
        $app->setResponseHeader('Header-Test', 'value2');

        try {
            $app->terminateHtml('', [
                'Header-Test' => 'value3'
            ]);
        } catch (ExitApplicationError $e) {

        }

        $this->assertSame('value3', $app->getResponse()->getHeaderLine('header-test'));
    }
}
