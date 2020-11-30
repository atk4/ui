<?php

declare(strict_types=1);

namespace atk4\ui;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class atk4\ui\Template is deprecated. Use atk4\ui\HtmlTemplate instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed in 2.5 version
 */
class Template extends \atk4\ui\HtmlTemplate
{
}
