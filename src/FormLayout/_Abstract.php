<?php

declare(strict_types=1);

namespace atk4\ui\FormLayout;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class atk4\ui\FormLayout\_Abstract is deprecated. Use atk4\ui\Form\AbstractLayout instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed dec-2020
 */
abstract class _Abstract extends \atk4\ui\Form\AbstractLayout
{
}
