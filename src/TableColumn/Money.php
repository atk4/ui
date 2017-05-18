<?php

namespace atk4\ui\TableColumn;

/**
 * Column for formatting money.
 */
class Money extends Generic
{
    /** @var bool Should we show zero values in cells? */
    public $show_zero_values = true;

    // overrides
    public $attr = ['all'=>['class'=>['right aligned single line']]];

    public function getDataCellHTML(\atk4\data\Field $f = null)
    {
        if (!isset($f)) {
            throw new Exception(['Money column requires a field']);
        }

        return $this->getTag(
            'body',
            '{$'.$f->short_name.'}',
            ['class'=> '{$_'.$f->short_name.'_money}']
        );
    }

    public function getHTMLTags($row, $field)
    {
        if ($field->get() < 0) {
            return ['_'.$field->short_name.'_money'=>'right aligned single line negative'];
        } elseif (!$this->show_zero_values && $field->get() == 0) {
            return ['_'.$field->short_name.'_money'=>'right aligned single line', $field->short_name=>'-'];
        }

        return ['_'.$field->short_name.'_money'=>'right aligned single line'];
    }
}
