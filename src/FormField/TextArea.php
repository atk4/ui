<?php

declare(strict_types=1);

namespace atk4\ui\FormField;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class atk4\ui\FormField\TextArea is deprecated. Use atk4\ui\Form\Control\Textarea instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed dec-2020
 */
class TextArea extends \atk4\ui\Form\Control\Textarea
{
}
