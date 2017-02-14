<?php

namespace atk4\ui\Column;

/**
 * Formatting money.
 */
class Money extends Generic
{
    public $currency = null;

    public $format = null;

    public $attr = ['all'=>['class'=>['right aligned single line']]];

    /**
     * Provided with a field definition (from a model) will return a header
     * cell, fully formatted to be included in a Grid. (<th>).
     *
     * Potentialy may include elements for sorting.
     */

    /**
     * Provided with a field definition will return a string containing a "Template"
     * that would procude <td> cell when rendered. Example output:.
     *
     *   <td><b>{$name}</b></td>
     *
     * The must correspond to the name of the field, although you can also use multiple tags. The tag
     * will also be formatted before inserting, see UI Persistence formatting in the documentation.
     *
     * This method will be executed only once per grid rendering, if you need to format data manually,
     * you should use $this->grid->addHook('formatRow');
     */
    public function getCellTemplate(\atk4\data\Field $f)
    {
        return $this->app->getTag(
            'td',
            ['class'=> '{$_'.$f->short_name.'_money}'],
            '{$'.$f->short_name.'}'
        );
    }

    public function getHTMLTags($row, $field)
    {
        if ($field->get() < 0) {
            return ['_'.$field->short_name.'_money'=>'right aligned single line negative'];
        }

        return ['_'.$field->short_name.'_money'=>'right aligned single line'];
    }
}
