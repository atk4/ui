<?php

declare(strict_types=1);

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;
use atk4\ui\App;
use atk4\ui\Template;


/**
 * Test if property templateClass is used correctly in loadTemplate().
 */
class AppUsesTemplateClassPropertyTest extends AtkPhpunit\TestCase
{
    public function testDefaultTemplateClassSetting()
    {
        $app = new App(['always_run' => false]);
        $template = $app->loadTemplate('html.html');
        $this->assertInstanceOf(
            Template::class,
            $template
        );
    }

    public function testOverwriteTemplateClassSetting()
    {
        $anotherTemplateClass = new class() extends Template {
        };
        $app = new App(['always_run' => false]);
        $app->templateClass = get_class($anotherTemplateClass);
        $template = $app->loadTemplate('html.html');
        $this->assertInstanceOf(
            get_class($anotherTemplateClass),
            $template
        );
    }
}
