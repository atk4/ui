<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Layout\Section;

use Atk4\Ui\Accordion as UiAccordion;
use Atk4\Ui\AccordionSection;
use Atk4\Ui\Form;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\View;

/**
 * Represents form controls in accordion.
 */
class Accordion extends UiAccordion
{
    /** @var array */
    public $formLayoutSeed = [Form\Layout::class];

    public Form $form;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        // add hook which in case of field error expands respective accordion sections
        $this->form->onHook(Form::HOOK_DISPLAY_ERROR, static function (Form $form, $fieldName, $str) {
            // default behavior
            $jsError = [$form->js()->form('add prompt', $fieldName, $str)];

            // if a form control is part of an accordion section, it will open that section
            $section = $form->getControl($fieldName)->getClosestOwner(AccordionSection::class);
            if ($section) {
                $jsError[] = $section->getOwner()->jsOpen($section);
            }

            return new JsBlock($jsError);
        });
    }

    /**
     * Return an accordion section with a form layout associate with a form.
     *
     * @return Form\Layout
     */
    #[\Override]
    public function addSection($title, ?\Closure $callback = null, $icon = 'dropdown')
    {
        $section = parent::addSection($title, $callback, $icon);

        $res = View::fromSeed($this->formLayoutSeed, ['form' => $this->form]);
        $section->add($res);

        return $res;
    }

    /**
     * @param AccordionSection|Form\Layout $section
     */
    #[\Override]
    public function getSectionIdx($section)
    {
        if (!$section instanceof AccordionSection) {
            $section = AccordionSection::assertInstanceOf($section->getOwner());
        }

        return parent::getSectionIdx($section);
    }
}
