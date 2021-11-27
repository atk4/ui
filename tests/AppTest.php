<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\App;
use Atk4\Ui\Exception\LateCliOnlyError;
use Atk4\Ui\HtmlTemplate;

class AppTest extends TestCase
{
    protected function getApp()
    {
        return new App([
            'catch_exceptions' => false,
            'always_run' => false,
        ]);
    }

    public function testTemplateClassDefault(): void
    {
        $app = $this->getApp();

        $this->assertInstanceOf(
            HtmlTemplate::class,
            $app->loadTemplate('html.html')
        );
    }

    public function testTemplateClassCustom(): void
    {
        $anotherTemplateClass = new class() extends HtmlTemplate {
        };

        $app = $this->getApp();
        $app->templateClass = get_class($anotherTemplateClass);

        $this->assertInstanceOf(
            get_class($anotherTemplateClass),
            $app->loadTemplate('html.html')
        );
    }

    public function testUnexpectedOutputLateError(): void
    {
        $app = $this->getApp();

        ob_start();
        try {
            echo 'test';

            $this->expectException(LateCliOnlyError::class);
            $this->expectExceptionMessage('Unexpected output detected.');
            $app->terminateHtml('');
        } finally {
            ob_end_clean();
        }
    }
}
