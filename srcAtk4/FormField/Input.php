<?php

declare(strict_types=1);

namespace Atk4\Ui\FormField;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class atk4\ui\FormField\Input is deprecated. Use atk4\ui\Form\Control\Input instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed dec-2020
 */
class Input extends \atk4\ui\Form\Control\Input
{
}
