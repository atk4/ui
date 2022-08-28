<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Table;

/**
 * Column for formatting country code as flags.
 */
class Flag extends Table\Column
{
    /** @var string Name of model field which contains country ALPHA-2 (2 letter) codes. */
    public $code_field;

    /** @var string|null Optional name of model field which contains country names. */
    public $name_field;

    public function getHtmlTags(Model $row, ?Field $field): array
    {
        $countryCode = $row->get($this->code_field);
        $countryName = $this->name_field ? $row->get($this->name_field) : null;

        return [
            $field->shortName => $countryCode === null ? '' : $this->getApp()->getTag('i', [
                'class' => strtolower($countryCode) . ' flag',
                'title' => $countryCode . ($countryName === null ? '' : ' - ' . $countryName),
            ]),
        ];
    }
}
