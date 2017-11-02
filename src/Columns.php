<?php

namespace atk4\ui;

/**
 * Imprements vertically distributed columns based on CSS Grid system.
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
    public $width = null;

    /**
     * Sum of all column widths added so far.
     *
     * @var int
     */
    protected $calculated_width = 0;

    /**
     * Allows Grid to calculate widths automatically.
     *
     * @var array
     */
    public $sizes = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven',
    'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', ];

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

        $size = $defaults[0];
        unset($defaults[0]);
        $defaults = array_merge(['View', null], $defaults);

        $column = $this->add($defaults);

        if ($size && isset($this->sizes[$size])) {
            $column->addClass($this->sizes[$size].' wide');
            $this->calculated_width = false;
        } elseif ($this->calculated_width !== false) {
            $this->calculated_width++;
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
        return $this->add(new static([$width, 'ui' => false]))->addClass('row');
    }

    public function renderView()
    {
        $width = $this->width ?: $this->calculated_width;
        if ($this->content) {
            $this->addClass($this->content);
            $this->content = null;
        }

        if (isset($this->sizes[$width])) {
            $this->addClass($this->sizes[$width].' column');
        }

        parent::renderView();
    }
}
