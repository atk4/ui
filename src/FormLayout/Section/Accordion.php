<?php

namespace atk4\ui\FormLayout\Section;

use atk4\ui\AccordionSection;

class Accordion extends \atk4\ui\Accordion
{
    public $formLayout = 'FormLayout/Generic';
    public $form = null;

    /**
     * Initialization.
     *
     * Adds hook which in case of field error expands respective accordion sections.
     */
    public function init()
    {
        parent::init();

        $this->form->onHook('displayError', function ($form, $fieldName, $str) {
            // default behavior
            $jsError = [$form->js()->form('add prompt', $fieldName, $str)];

            // if field is part of an accordion section, will open that section.
            $section = $form->getClosestOwner($form->getField($fieldName), AccordionSection::class);
            if ($section) {
                $jsError[] = $section->owner->jsOpen($section);
            }

            return $jsError;
        });
    }

    /**
     * Return an accordion section with a form layout associate with a form.
     *
     * @param string        $title
     * @param null|callable $callback
     * @param string        $icon
     *
     * @throws \atk4\ui\Exception
     *
     * @return \atk4\ui\FormLayout\Generic
     */
    public function addSection($title, $callback = null, $icon = 'dropdown')
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
        if ($section instanceof \atk4\ui\AccordionSection) {
            return parent::getSectionIdx($section);
        }

        return parent::getSectionIdx($section->owner);
    }
}
