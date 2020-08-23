<?php

declare(strict_types=1);

namespace atk4\ui\Layout;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class atk4\ui\Layout\Generic is deprecated. Use atk4\ui\Layout instead', E_USER_DEPRECATED);
}

class Generic extends \atk4\ui\Layout
{
}
