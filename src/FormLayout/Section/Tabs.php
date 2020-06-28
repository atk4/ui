<?php

declare(strict_types=1);

namespace atk4\ui\FormLayout\Section;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class atk4\ui\FormLayout\Section\Tabs is deprecated. Use atk4\ui\Form\Layout\Section\Tabs instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed dec-2020
 */
class Tabs extends \atk4\ui\Form\Layout\Section\Tabs
{
}
