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
        $res = str_replace("\u{00a0}" /* Unicode NBSP */, ' ', $res);

        return trim(str_replace($this->getApp()->ui_persistence->currency, '', $res ?? null));
    }

    protected function renderView(): void
    {
        if ($this->label === null) {
            $this->label = $this->getApp()->ui_persistence->currency;
        }

        parent::renderView();
    }
}
