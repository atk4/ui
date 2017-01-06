<?php

namespace atk4\ui\FormField;

use atk4\ui\Form;

/**
 * Input element for a form field.
 */
class Radio extends Generic
{
    public $ui = 'radio checkbox';

    public $defaultTemplate = 'formfield/radio.html';

    function setModel($m)
    {
        $this->add(new \atk4\ui\Lister(), 'Radio')->setModel($m);
    }

    function renderView() {

        $this->js(true)->checkbox();

        return parent::renderView();
    }
}
