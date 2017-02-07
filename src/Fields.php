<?php

namespace atk4\ui;

class Fields extends View
{
    public $_class = 'buttons';

    public $form = null;

    public $label = null;

    public function addField(...$args)
    {
        return $this->add($form->fieldFactory(...$args));
    }

    public function renderView()
    {
        parent::renderView();
    }
}
