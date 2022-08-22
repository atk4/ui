<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Core\Factory;

/**
 * Vertically distributed columns based on CSS Grid system.
 */
class Columns extends View
{
    public $ui = 'grid';

    /**
     * Explicitly specify the width of all columns. Normally that's 16, but
     * semantic-ui allows you to override with 5 => "ui five column grid".
     *
     * @var int
     */
    public $width;

    /** @var int|false Sum of all column widths added so far. */
    protected $calculatedWidth = 0;

    /** @var array Allows Grid to calculate widths automatically. */
    public $sizes = [
        '', 'one', 'two', 'three', 'four', 'five', 'six', 'seven',
        'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen',
    ];

    /**
     * Add new vertical column.
     *
     * @param int|array $defaults specify width (1..16) or relative to $width
     */
    public function addColumn($defaults = null)
    {
        if (!is_array($defaults)) {
            $defaults = [$defaults];
        }

        $size = $defaults[0] ?? null;
        unset($defaults[0]);

        $column = Factory::factory([View::class], $defaults);
        $this->add($column);

        if ($size && isset($this->sizes[$size])) {
            $column->addClass($this->sizes[$size] . ' wide');
            $this->calculatedWidth = false;
        } elseif ($this->calculatedWidth !== false) {
            ++$this->calculatedWidth;
        }
        $column->addClass('column');

        return $column;
    }

    /**
     * Adds a new row to your grid system. You can specify width of this row
     * which will default to 16.
     *
     * @param int $width
     */
    public function addRow($width = null)
    {
        return static::addTo($this, [$width, 'ui' => false])->addClass('row');
    }

    protected function renderView(): void
    {
        $width = $this->width ?: $this->calculatedWidth;
        if ($this->content) {
            $this->addClass($this->content);
            $this->content = null;
        }

        if (isset($this->sizes[$width])) {
            $this->addClass($this->sizes[$width] . ' column');
        }

        parent::renderView();
    }
}
