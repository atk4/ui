<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Table;

/**
 * If field have values without a relation like a status or a coded state of a process, example:
 * Machine state:
 *  0 => off
 *  1 => powerup
 *  2 => on
 *  3 => resetting
 *  4 => error.
 *
 * we don't need a table to define this, cause are defined in project
 *
 * using KeyValue Column you can show this values without using DB Relations
 * need to be defined in field like this:
 *
 * $this->addField('course_payment_status', [
 *    'caption' => __('Payment Status'),
 *    'default' => 0,
 *    'values' => [
 *        __('not invoiceable'),
 *        __('ready to invoice'),
 *        __('invoiced'),
 *        __('paid'),
 *    ],
 *    'ui' => [
 *        'form' => [Form\Control\Dropdown::class],
 *        'table' => ['KeyValue'],
 *    ],
 * ]);
 */
class KeyValue extends Table\Column
{
    public array $values;

    public function getHtmlTags(Model $row, ?Field $field): array
    {
        $key = $field->get($row);
        $value = $field->values[$key] ?? '';

        return [$field->shortName => $value];
    }
}
