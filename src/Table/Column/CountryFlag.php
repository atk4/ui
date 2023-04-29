<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Table;

class CountryFlag extends Table\Column
{
    /** Name of country code model field (in ISO 3166-1 alpha-2 format) */
    public ?string $codeField = null;

    /** Optional name of model field which contains full country name. */
    public ?string $nameField = null;

    public function getHtmlTags(Model $row, ?Field $field): array
    {
        $countryCode = $row->get($this->codeField ?? $field->shortName);
        $countryName = $this->nameField ? $row->get($this->nameField) : null;

        return [
            $field->shortName => $countryCode === null
                ? ''
                : $this->getApp()->getTag('i', [
                    'class' => strtolower($countryCode) . ' flag',
                    'title' => strtoupper($countryCode) . ($countryName === null ? '' : ' - ' . $countryName),
                ]),
        ];
    }
}
