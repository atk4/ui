<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

/**
 * Input element for a form control.
 */
class Money extends Input
{
    public function getValue()
    {
        $res = parent::getValue();

        return $res === null ? null : trim(str_replace($this->getApp()->ui_persistence->currency, '', $res), ' ' . "\u{00a0}" /* Unicode NBSP */);
    }

    protected function renderView(): void
    {
        if ($this->label === null) {
            $this->label = $this->getApp()->ui_persistence->currency;
        }

        parent::renderView();
    }
}
