<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\AtkPhpunit;
use Atk4\Ui\App;
use Atk4\Ui\HtmlTemplate;

class AppTest extends AtkPhpunit\TestCase
{
    protected function getApp()
    {
        return new App([
            'catch_exceptions' => false,
            'always_run' => false,
        ]);
    }

    public function testTemplateClassDefault()
    {
        $app = $this->getApp();

        $this->assertInstanceOf(
            HtmlTemplate::class,
            $app->loadTemplate('html.html')
        );
    }

    public function testTemplateClassCustom()
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
}
