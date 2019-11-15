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

        // Override global ui_persistence with value defined in data model field ui[persistence][currency_decimnals]
        $currency_decimals = isset($this->field->ui['persistence']['currency_decimals'])
            ? $this->field->ui['persistence']['currency_decimals']
            : $this->app->ui_persistence->currency_decimals;

        return number_format($v, $currency_decimals);
    }

    public function renderView()
    {
        // Override global ui_persistence with value defined in data model field ui[persistence][currency]
        $currency = isset($this->field->ui['persistence']['currency'])
            ? $this->field->ui['persistence']['currency']
            : $this->app->ui_persistence->currency;

        if ($this->label === null) {
            $this->label = $currency;
        }

        parent::renderView();
    }
}
