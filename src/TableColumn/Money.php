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

    public function getTagAttributes($position, $attr = [])
    {
        $attr = array_merge_recursive($attr, ['class'=>['{$_'.$this->short_name.'_class}']]);

        return parent::getTagAttributes($position, $attr);
    }

    public function getDataCellHTML(\atk4\data\Field $f = null, $extra_tags = [])
    {
        if (!isset($f)) {
            throw new Exception(['Money column requires a field']);
        }

        return $this->getTag(
            'body',
            '{$'.$f->short_name.'}',
            $extra_tags
        );
    }

    public function getHTMLTags($row, $field)
    {
        if ($field->get() < 0) {
            return ['_'.$this->short_name.'_class'=>'negative'];
        } elseif (!$this->show_zero_values && $field->get() == 0) {
            return ['_'.$this->short_name.'_class'=>'', $field->short_name=>'-'];
        }

        return ['_'.$this->short_name.'_class'=>''];
    }
}
