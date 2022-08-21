<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Ui\Table;

/**
 * Column for formatting image.
 */
class Image extends Table\Column
{
    /** @var array Overrides custom attributes that will be applied on head, body or foot. */
    public $attr = ['all' => ['class' => ['center aligned single line']]];

    public function getDataCellTemplate(Field $field = null)
    {
        $caption = $field ? $field->getCaption() : $this->shortName;

        return '<img src="' . parent::getDataCellTemplate($field) . '" alt="' . $caption . '" border="0" />';
    }
}
