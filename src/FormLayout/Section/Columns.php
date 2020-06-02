<?php

namespace atk4\ui\FormLayout\Section;

class Columns extends \atk4\ui\Columns
{
    public $formLayout = \atk4\ui\FormLayout\Generic::class;
    public $form;

    /**
     * Add new vertical column.
     *
     * @param int|array $defaults specify width (1..16) or relative to $width
     *
     * @return \atk4\ui\FormLayout\Generic
     */
    public function addColumn($defaults = null)
    {
        $c = parent::addColumn($defaults);

        return $c->add([$this->formLayout, 'form' => $this->form]);
    }
}
