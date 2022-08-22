<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Layout\Section;

use Atk4\Ui\Accordion as UiAccordion;
use Atk4\Ui\AccordionSection;
use Atk4\Ui\Form;

/**
 * Represents form controls in accordion.
 */
class Accordion extends UiAccordion
{
    /** @var class-string<Form\Layout> */
    public $formLayout = Form\Layout::class;

    public Form $form;

    /**
     * Adds hook which in case of field error expands respective accordion sections.
     */
    protected function init(): void
    {
        parent::init();

        $this->form->onHook(Form::HOOK_DISPLAY_ERROR, function (Form $form, $fieldName, $str) {
            // default behavior
            $jsError = [$form->js()->form('add prompt', $fieldName, $str)];

            // if a form control is part of an accordion section, it will open that section.
            $section = $form->getClosestOwner($form->getControl($fieldName), AccordionSection::class);
            if ($section) {
                $jsError[] = $section->getOwner()->jsOpen($section);
            }

            return $jsError;
        });
    }

    /**
     * Return an accordion section with a form layout associate with a form.
     *
     * @param string $title
     * @param string $icon
     *
     * @return Form\Layout
     */
    public function addSection($title, \Closure $callback = null, $icon = 'dropdown')
    {
        $section = parent::addSection($title, $callback, $icon);

        return $section->add([$this->formLayout, 'form' => $this->form]);
    }

    /**
     * Return a section index.
     *
     * @param AccordionSection $section
     *
     * @return int
     */
    public function getSectionIdx($section)
    {
        if ($section instanceof AccordionSection) {
            return parent::getSectionIdx($section);
        }

        return parent::getSectionIdx($section->getOwner());
    }
}
