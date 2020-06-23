<?php

declare(strict_types=1);

namespace atk4\ui\FormField;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Use atk4\ui\Form\Field\Calendar instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed jun-2021
 */
class Calendar extends \atk4\ui\Form\Field\Calendar
{
}
