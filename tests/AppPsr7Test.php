<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\App;
use Atk4\Ui\Exception\ExitApplicationError;
use Atk4\Ui\Layout;

class AppPsr7Test extends TestCase
{
    /**
     * @var App
     */
    private $app;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = new class(['call_exit' => false, 'catch_exceptions' => false, 'always_run' => false]) extends App {
            protected function emitResponse(): void
            {
                // no emitting to allow fast unit test
            }
        };
        $this->app->initLayout([Layout::class]);
    }

    public function testResponseHeaders(): void
    {
        $this->app->setResponseHeader('heAder-test', 'value1');
        $this->app->setResponseHeader('Header-Test', 'value2');

        try {
            $this->app->terminateHtml('', [
                'Header-Test' => 'value3',
            ]);
        } catch (ExitApplicationError $e) {
        }

        $this->assertSame('value3', $this->app->getResponse()->getHeaderLine('header-test'));
    }

    public function testResponseHeadersRespectCase(): void
    {
        $this->app->setResponseHeader('heAder-test', 'value1');
        $this->app->setResponseHeader('Header-Test', 'value2');

        try {
            $this->app->terminateHtml('', [
                'Header-Test' => 'value3',
            ]);
        } catch (ExitApplicationError $e) {
        }

        $this->assertSame('value3', $this->app->getResponse()->getHeaderLine('header-test'));
    }

    public function testResponseHeadersRespectContentOnTerminate(): void
    {
        $this->app->setResponseHeader('content-type', 'text/plain');

        try {
            $this->app->terminateHtml('', [
                'Content-type' => 'application/json',
            ]);
        } catch (ExitApplicationError $e) {
        }

        $this->assertSame('text/html', $this->app->getResponse()->getHeaderLine('Content-Type'));
    }
}
