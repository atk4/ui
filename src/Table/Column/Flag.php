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
    public $codeField;

    /** @var string|null Optional name of model field which contains country names. */
    public $nameField;

    public function getHtmlTags(Model $row, ?Field $field): array
    {
        $countryCode = $row->get($this->codeField);
        $countryName = $this->nameField ? $row->get($this->nameField) : null;

        return [
            $field->shortName => $countryCode === null ? '' : $this->getApp()->getTag('i', [
                'class' => strtolower($countryCode) . ' flag',
                'title' => strtoupper($countryCode) . ($countryName === null ? '' : ' - ' . $countryName),
            ]),
        ];
    }
}
