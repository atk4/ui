<?php

namespace atk4\ui;

class Fields extends View
{
    public $_class = 'buttons';

    public $form = null;

    public $label = null;

    function addField(...$args)
    {
        return $this->add($form->fieldFactory(...$args));
    }

    function renderView()
    {
        parent::renderView();
    }
}
