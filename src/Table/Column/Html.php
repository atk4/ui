<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Table;

/**
 * Use this decorator if you have HTML code that you just want to put into the table cell.
 */
class Html extends Table\Column
{
    public function getDataCellHtml(Field $field = null, array $attr = []): string
    {
        return '{$_' . $field->shortName . '}';
    }

    public function getHtmlTags(Model $row, ?Field $field): array
    {
        return ['_' . $field->shortName => '<td>' . $row->get($field->shortName) . '</td>'];
    }
}
