<?php

declare(strict_types=1);

namespace atk4\ui\FormLayout;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class atk4\ui\FormLayout\Custom is deprecated. Use atk4\ui\Form\Layout\Custom instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed dec-2020
 */
class Custom extends \atk4\ui\Form\Layout\Custom
{
}
