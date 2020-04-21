<?php

namespace atk4\ui\TableColumn;

use atk4\data\Field;

/**
 * Class HTML
 * 
 * Use this decorator if you have HTML code that you just want to put into the table cell.
 */
class HTML extends Generic
{

    /**
     * Replace parent method.
     *
     * @param Field $field
     *
     * @return string
     */
    public function getDataCellHTML(Field $field = null, $extra_tags = [])
    {
        return '{$_'.$field->short_name.'}';
    }

    /**
     * Replace parent method.
     *
     * @param Model|array $row   link to row data
     * @param Field|null  $field field being rendered
     *
     * @return array Associative array with tags and their HTML values.
     */
    public function getHTMLTags($row, $field)
    {
        return ['_'.$field->short_name => '<td>'.$row->get($field).'</td>'];
    }
}
