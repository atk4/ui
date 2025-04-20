<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

class Textarea extends Input
{
    public string $inputType = 'textarea';

    /** @var int Text area vertical size */
    public $rows = 2;

    #[\Override]
    public function getInputTag(): string
    {
        return $this->getApp()->getTag('textarea', array_merge([
            'name' => $this->shortName,
            'rows' => $this->rows,
            'placeholder' => $this->placeholder,
            'id' => $this->name . '_input',
            'disabled' => $this->disabled,
            'readonly' => $this->readOnly && !$this->disabled,
        ], $this->inputAttr), $this->getInputValue() ?? '');
    }
}
