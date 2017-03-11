<?php

namespace atk4\ui;

/**
 * Imprements vertically distributed columns based on CSS Grid system.
 */
class Columns extends View
{
    public $ui = 'grid';

    public $width = null;

    public $calculated_width = 0;

    /**
     * Allows Grid to calculate widths automatically.
     */
    public $sizes = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven',
    'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', ];

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

    public function addRow($width = null)
    {
        return $this->add(new static([$width, 'ui'=>false]))->addClass('row');
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
