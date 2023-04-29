<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

class Money extends Input
{
    public string $inputType = 'text';

    public function getValue()
    {
        $res = parent::getValue();
        if ($res === null) {
            return null;
        }

        $res = str_replace("\u{00a0}" /* Unicode NBSP */, ' ', $res);

        return trim(str_replace($this->getApp()->uiPersistence->currency, '', $res));
    }

    protected function renderView(): void
    {
        if ($this->label === null) {
            $this->label = $this->getApp()->uiPersistence->currency;
        }

        parent::renderView();
    }
}
