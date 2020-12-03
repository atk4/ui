<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Table;

/**
 * Class HTML.
 *
 * Use this decorator if you have HTML code that you just want to put into the table cell.
 */
class Html extends Table\Column
{
    /**
     * Replace parent method.
     *
     * @param Field $field
     *
     * @return string
     */
    public function getDataCellHtml(Field $field = null, $extra_tags = [])
    {
        return '{$_' . $field->short_name . '}';
    }

    /**
     * Replace parent method.
     *
     * @param Model      $row   link to row data
     * @param Field|null $field field being rendered
     *
     * @return array associative array with tags and their HTML values
     */
    public function getHtmlTags(Model $row, $field)
    {
        return ['_' . $field->short_name => '<td>' . $row->get($field->short_name) . '</td>'];
    }
}
