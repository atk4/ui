<?php

declare(strict_types=1);

namespace Atk4\Ui\FormLayout\Section;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class atk4\ui\FormLayout\Section\Generic is deprecated. Use atk4\ui\Form\Layout\Section instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed dec-2020
 */
class Generic extends \atk4\ui\Form\Layout\Section
{
}
