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

        if ($v === null) {
            return;
        }

        return number_format($v, $this->app->ui_persistence->currency_decimals);
    }

    public function renderView()
    {
        if ($this->label === null) {
            $this->label = $this->app->ui_persistence->currency;
        }

        parent::renderView();
    }
}
