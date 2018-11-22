<?php

namespace atk4\ui\FormLayout\Section;

class Generic extends \atk4\ui\View
{
    public $form = null;
    public $formLayout = 'FormLayout/Generic';

    public function addSection()
    {
        return $this->add([$this->formLayout, 'form' => $this->form]);
    }
}