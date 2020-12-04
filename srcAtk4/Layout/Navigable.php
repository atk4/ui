<?php

declare(strict_types=1);
/**
 * Interface for a Layout using a navigable side menu.
 */

namespace Atk4\Ui\Layout;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Interface atk4\ui\Layout\Navigable is deprecated. Use atk4\ui\NavigableInterface instead', E_USER_DEPRECATED);
}

interface Navigable extends NavigableInterface
{
}
