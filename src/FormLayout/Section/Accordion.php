<?php

namespace atk4\ui\FormLayout\Section;

class Accordion extends \atk4\ui\Accordion
{
    public $formLayout = 'FormLayout/Generic';
    public $form = null;

    public function addSection($title, $icon = 'dropdown')
    {
        $section = parent::addSection($title, null, $icon);

        return $section->add([$this->formLayout, 'form' => $this->form]);
    }

    public function getSectionIdx($section)
    {
        return parent::getSectionIdx($section->owner);
    }
}
