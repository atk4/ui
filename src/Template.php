<?php

declare(strict_types=1);

namespace Atk4\Ui;

if (!class_exists(\SebastianBergmann\CodeCoverage\CodeCoverage::class, false)) {
    'trigger_error'('Class Atk4\Ui\Template is deprecated. Use Atk4\Ui\HtmlTemplate instead', E_USER_DEPRECATED);
}

/**
 * @deprecated will be removed in 2.5 version
 */
class Template extends \Atk4\Ui\HtmlTemplate
{
}
