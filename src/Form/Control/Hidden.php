<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

class Hidden extends Input
{
    public $ui = '';

    public bool $layoutWrap = false;

    public string $inputType = 'hidden';
}
