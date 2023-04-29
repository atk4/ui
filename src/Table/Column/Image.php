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
    public array $attr = ['all' => ['class' => ['center aligned single line']]];

    public function getDataCellTemplate(Field $field = null): string
    {
        $caption = $field ? $field->getCaption() : $this->shortName;

        return $this->getApp()->getTag('img/', ['src' => parent::getDataCellTemplate($field), 'alt' => $caption, 'border' => '0']);
    }
}
