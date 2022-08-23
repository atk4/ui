<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Ui\Table;

/**
 * Implements Column helper for grid.
 */
class Template extends Table\Column
{
    /** @var string User-defined template for this Column. */
    public $template;

    /**
     * Call new Table\Column\Template('{$name} {$surname}');.
     *
     * @param string $template Template with {$tags}
     */
    public function __construct($template)
    {
        $this->template = $template;
    }

    public function getDataCellTemplate(Field $field = null): string
    {
        return $this->template;
    }
}
