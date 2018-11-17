<?php

namespace atk4\ui\FormLayout\Section;

class Column extends \atk4\ui\Columns
{
    public $formLayout = 'FormLayout/Generic';
    public $form = null;

    public function addColumn($defaults = null)
    {
        $c = parent::addColumn($defaults);

        return $c->add([$this->formLayout, 'form' => $this->form]);
    }
}
