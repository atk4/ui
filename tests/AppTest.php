<?php

declare(strict_types=1);

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;
use atk4\ui\App;
use atk4\ui\Template;

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
            Template::class,
            $app->loadTemplate('html.html')
        );
    }

    public function testTemplateClassCustom()
    {
        $anotherTemplateClass = new class() extends Template {
        };

        $app = $this->getApp();
        $app->templateClass = get_class($anotherTemplateClass);

        $this->assertInstanceOf(
            get_class($anotherTemplateClass),
            $app->loadTemplate('html.html')
        );
    }
}
