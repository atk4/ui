<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\App;
use Atk4\Ui\Exception\ExitApplicationError;
use Atk4\Ui\Layout;

class AppPsr7Test extends TestCase
{
    protected function getApp(): App
    {
        $app = new class(['catchExceptions' => false, 'alwaysRun' => false, 'callExit' => false]) extends App {
            protected function emitResponse(): void
            {
                // no emit
            }
        };

        $app->initLayout([Layout::class]);

        return $app;
    }

    public function testResponseHeadersRespectCase(): void
    {
        $app = $this->getApp();
        $app->setResponseHeader('heAder-test', 'value1');
        $app->setResponseHeader('Header-Test', 'value2');

        try {
            $app->run();
        } catch (ExitApplicationError $e) {
        }

        $this->assertSame('value2', $app->getResponse()->getHeaderLine('header-test'));
    }

    public function testResponseHeadersRespectContentOnTerminate(): void
    {
        $app = $this->getApp();
        $app->setResponseHeader('content-type', 'text/plain');

        try {
            $app->run(); // it will override content-type with text/html
        } catch (ExitApplicationError $e) {
        }

        $this->assertSame('text/html', $app->getResponse()->getHeaderLine('Content-Type'));
    }
}
