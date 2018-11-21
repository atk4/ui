<?php

namespace atk4\ui\FormLayout\Section;

class Tabs extends \atk4\ui\Tabs
{
    public $formLayout = 'FormLayout/Generic';
    public $form = null;

    public function addTab($name)
    {
        $c = parent::addTab($name);

        return $c->add([$this->formLayout, 'form' => $this->form]);
    }
}
