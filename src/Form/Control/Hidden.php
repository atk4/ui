<?php

declare(strict_types=1);

namespace atk4\ui\Form\Control;

/**
 * Input element for a form control.
 */
class Hidden extends Input
{
    public $ui = '';
    public $layoutWrap = false;
    public $inputType = 'hidden';
}
