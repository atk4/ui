<?php

declare(strict_types=1);

namespace Atk4\Ui\Form\Control;

/**
 * Input element for a form control.
 */
class Hidden extends Input
{
    public $ui = '';
    public $layoutWrap = false;
    public $inputType = 'hidden';
}
