<?php

namespace atk4\ui\FormLayout\Section;

use atk4\ui\AccordionSection;

class Accordion extends \atk4\ui\Accordion
{
    public $formLayout = 'FormLayout/Generic';
    public $form = null;

    /**
     * Return an accordion section with a form layout associate with a form.
     *
     * @param string $title
     * @param string $icon
     *
     * @return AccordionSection|\atk4\ui\View
     * @throws \atk4\ui\Exception
     */
    public function addSection($title, $icon = 'dropdown')
    {
        $section = parent::addSection($title, null, $icon);

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
        } else {
            return parent::getSectionIdx($section->owner);
        }
    }
}
