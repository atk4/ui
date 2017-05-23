<?php

namespace atk4\ui\FormField;

use atk4\ui\Form;

/**
 * Input element for a form field.
 */
class Money extends Input
{
    public function getValue()
    {
        $v = $this->field ? $this->field->get() : ($this->content ?: null);

        if (is_null($v)) {
            return;
        }

        return number_format($v, 2);
    }

    public function renderView()
    {
        if ($this->label === null) {
            $this->label = $this->app->ui_persistence->currency;
        }

        parent::renderView();
    }
}
