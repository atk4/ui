<?php

declare(strict_types=1);

namespace atk4\ui\Form\Control;

/**
 * Input element for a form control.
 */
class Money extends Input
{
    public function getValue()
    {
        $v = $this->field ? $this->field->get() : ($this->content ?: null);

        if ($v === null) {
            return;
        }

        return number_format($v, \atk4\ui\Persistence\Type\Money::getProps('decimal'));
    }

    protected function renderView(): void
    {
        if ($this->label === null) {
            $this->label = \atk4\ui\Persistence\Type\Money::getProps('currency');
        }

        parent::renderView();
    }
}
