<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests\Form;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\HtmlTemplate;
use Atk4\Ui\Tests\CreateAppTrait;
use Atk4\Ui\ViewWithContent;

class LayoutTest extends TestCase
{
    use CreateAppTrait;

    public function testRecursiveRenderRespectsRegions(): void
    {
        $template = new HtmlTemplate(<<<'EOD'
            <div id="defaultRegion">{$Content}{$Buttons}</div>
            <div id="anotherRegion">{$input2}{$AnotherRegion}</div>
            EOD);
        $form = new Form(['buttonSave' => false]);
        $form->setApp($this->createApp());
        $form->invokeInit();
        $form->layout->template = $template;

        $m = new Model();
        $m->addField('input1');
        $m->addField('input2');
        $form->setEntity($m->createEntity());

        // all of these should be rendered inside #defaultRegion
        Button::addTo($form->layout, ['Button1']);
        ViewWithContent::addTo($form->layout, ['View1']);
        Form\Layout::addTo($form->layout, ['label' => 'Layout1']);

        // all of these should be rendered inside #anotherRegion
        Button::addTo($form->layout, ['Button2'], ['AnotherRegion']);
        ViewWithContent::addTo($form->layout, ['View2'], ['AnotherRegion']);
        Form\Layout::addTo($form->layout, ['label' => 'Layout2'], ['AnotherRegion']);

        $renderedHtml = $form->getHtml();
        $expectedHtml = <<<'EOF'

            <div id="atk" class="ui form">
            <form id="atk_form"></form>

            <div id="defaultRegion">
            <div class=" field">
              <label for="atk_form_layout_input1_input">Input 1</label>
            <div id="atk_form_layout_input1" class="ui input">
            <input form="atk_form" name="input1" id="atk_form_layout_input1_input" value="">
            </div>

            EOF . '  ' . <<<'EOF'

            </div><div id="atk_form_layout_button" class="ui button">Button1</div><div id="atk_form_layout_viewwithcontent">View1</div>
            <div class=" field atk-form-group">
              <label>Layout1</label>
              <div class="  fields">

            </div>
            </div></div>
            <div id="anotherRegion">
            <div class=" field">
              <label for="atk_form_layout_input2_input">Input 2</label>
            <div id="atk_form_layout_input2" class="ui input">
            <input form="atk_form" name="input2" id="atk_form_layout_input2_input" value="">
            </div>

            EOF . '  ' . <<<'EOF'

            </div><div id="atk_form_layout_button_2" class="ui button">Button2</div><div id="atk_form_layout_viewwithcontent_2">View2</div>
            <div class=" field atk-form-group">
              <label>Layout2</label>
              <div class="  fields">

            </div>
            </div></div></div>
            EOF;

        self::assertSame($expectedHtml, $renderedHtml);
    }
}
