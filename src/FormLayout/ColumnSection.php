<?php

namespace atk4\ui\FormLayout;

class ColumnSection extends \atk4\ui\Columns
{
    public $formLayout = 'FormLayout/Generic';
    public $form = null;

    public function addColumn($defaults = null)
    {
        $c = parent::addColumn($defaults);

        return $c->add([$this->formLayout, 'form' => $this->form]);
    }
}
