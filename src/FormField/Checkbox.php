<?php

namespace atk4\ui\FormField;

use atk4\ui\Form;

/**
 * Input element for a form field.
 */
class Checkbox extends Generic
{
    public $ui = 'checkbox';

    public $defaultTemplate = 'formfield/checkbox.html';

    public function renderView()
    {
        $this->js(true)->checkbox();

        return parent::renderView();
    }
}
