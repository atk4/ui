<?php

declare(strict_types=1);

namespace atk4\ui\FormField;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class atk4\ui\FormField\Hidden is deprecated. Use atk4\ui\Form\Control\Hidden instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed dec-2020
 */
class Hidden extends \atk4\ui\Form\Control\Hidden
{
}
