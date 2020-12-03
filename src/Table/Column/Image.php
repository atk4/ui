<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Ui\Table;

/**
 * Column for formatting image.
 */
class Image extends Table\Column
{
    /**
     * Overrides custom attributes that will be applied on head, body or foot.
     *
     * @var array
     */
    public $attr = ['all' => ['class' => ['center aligned single line']]];

    /**
     * Extend parent method.
     *
     * @return string
     */
    public function getDataCellTemplate(\Atk4\Data\Field $field = null)
    {
        $caption = $field ? $field->getCaption() : $this->short_name;

        return '<img src="' . parent::getDataCellTemplate($field) . '" alt="' . $caption . '" border="0" />';
    }
}
