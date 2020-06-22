<?php

declare(strict_types=1);

namespace atk4\ui\FormLayout;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Use atk4\ui\Form\AbstractLayout instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed jun-2021
 */
abstract class _Abstract extends \atk4\ui\Form\AbstractLayout
{
}
