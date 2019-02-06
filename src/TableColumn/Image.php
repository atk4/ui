<?php

namespace atk4\ui\TableColumn;

/**
 * Column for formatting image.
 */
class Image extends \atk4\ui\TableColumn\Generic
{
    // overrides
    public $attr = ['all' => ['class' => ['center aligned single line']]];

    public function getDataCellTemplate(\atk4\data\Field $f = null)
    {
        return '<img src="'.parent::getDataCellTemplate($f).'" />';
    }
}
