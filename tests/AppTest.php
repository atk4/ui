<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\App;
use Atk4\Ui\Exception\LateOutputError;
use Atk4\Ui\HtmlTemplate;

class AppTest extends TestCase
{
    protected function createApp(): App
    {
        return new App([
            'catchExceptions' => false,
            'alwaysRun' => false,
        ]);
    }

    public function testTemplateClassDefault(): void
    {
        $app = $this->createApp();

        static::assertInstanceOf(
            HtmlTemplate::class,
            $app->loadTemplate('html.html')
        );
    }

    public function testTemplateClassCustom(): void
    {
        $anotherTemplateClass = new class() extends HtmlTemplate {
        };

        $app = $this->createApp();
        $app->templateClass = get_class($anotherTemplateClass);

        static::assertInstanceOf(
            get_class($anotherTemplateClass),
            $app->loadTemplate('html.html')
        );
    }

    public function testUnexpectedOutputLateError(): void
    {
        $app = $this->createApp();

        ob_start();
        $testStr = 'direct output test';
        try {
            echo $testStr;

            $this->expectException(LateOutputError::class);
            $this->expectExceptionMessage('Unexpected output detected');
            $app->terminateHtml('');
        } finally {
            static::assertSame($testStr, ob_get_contents());
            ob_end_clean();
        }
    }
}
