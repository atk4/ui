<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Table;

/**
 * Sometime we need null values in DB. When we display values we have holes
 * with NoValue decorator we can show a display value for column null value.
 *
 * $this->addField('field', [
 *     [...]
 *     'ui' => [
 *         [...],
 *         'table' => [
 *             'NoValue', ' if empty display this value '
 *         ],
 *     ],
 * ]);
 */
class NoValue extends Table\Column
{
    /** @var string */
    public $noValue = ' --- ';

    public function getHtmlTags(Model $row, ?Field $field): array
    {
        $actualValue = $field->get($row);

        if ($actualValue === null || $actualValue === '') {
            return [$field->shortName => $this->noValue];
        }

        return parent::getHtmlTags($row, $field);
    }
}
