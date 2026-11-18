<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column\FilterModel;

use Atk4\Data\Model;
use Atk4\Ui\Table\Column;

abstract class AbstractTypeDatetime extends Column\FilterModel
{
    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->op->values = [
            '=' => 'Is',
            '!=' => 'Is not',
            'within' => 'Within',
            '<' => 'Before',
            '>' => 'After',
            '<=' => 'Before or on',
            '>=' => 'After or on',
            'empty' => 'Is empty',
            'not empty' => 'Is not empty',
        ];
        $this->op->default = '=';

        // the date value to operate on
        $this->value->values = [
            'today' => 'Today',
            'tomorrow' => 'Tomorrow',
            'yesterday' => 'Yesterday',
            '-1 week' => 'One week ago',
            '+1 week' => 'One week from now',
            '-1 month' => 'One month ago',
            '+1 month' => 'One month from now',
            'x_day_ago' => 'Number of days ago',
            'x_day_now' => 'Number of days from now',
            'exact' => 'Exact date',
        ];

        // the range value field use when within is select
        $this->addField('range', [
            'ui' => ['caption' => ''],
            'values' => [
                '-1 week' => 'The past week',
                '+1 week' => 'The next week',
                '-1 month' => 'The past month',
                '+1 month' => 'The next month',
                '-1 year' => 'The past year',
                '+1 year' => 'The next year',
                'x_day_before' => 'The next number of days before',
                'x_day_after' => 'The next number of days after',
            ],
        ]);

        // the exact date field input when exact is select as input value
        $this->addField('exact_date', ['type' => $this->lookupField->type, 'ui' => ['caption' => '']]);

        // the integer field to generate a date when x day selector is used
        $this->addField('number_days', ['type' => 'integer', 'ui' => ['caption' => '', 'placeholder' => 'days']]);

        $this->onHook(self::HOOK_VALIDATE, static function (Model $entity) {
            $errors = [];
            if (!in_array($entity->get('op'), ['empty', 'not empty'], true) && $entity->get('value') === null) {
                $errors['value'] = 'Value required';
            }
            if ($entity->get('op') === 'within' && $entity->get('range') === null) {
                $errors['range'] = 'Value required';
            }
            if ($entity->get('value') === 'exact' && $entity->get('exact_date') === null) {
                $errors['exact_date'] = 'Value required';
            }
            if (
                (
                    in_array($entity->get('value'), ['x_day_ago', 'x_day_now'], true)
                    || in_array($entity->get('range'), ['x_day_before', 'x_day_after'], true)
                )
                && $entity->get('number_days') === null
            ) {
                $errors['number_days'] = 'Value required';
            }

            return $errors;
        });
    }

    /**
     * Get date object according to it's modifier string.
     */
    public function getDatetime(string $dateModifier, ?bool $ceilTimeIfNotExact): ?\DateTime
    {
        if ($dateModifier === 'exact') {
            $date = $this->get('exact_date');
        } else {
            if ($dateModifier === 'x_day_ago' || $dateModifier === 'x_day_before') {
                $dateModifier = '-' . $this->get('number_days') . ' days';
            } elseif ($dateModifier === 'x_day_now' || $dateModifier === 'x_day_after') {
                $dateModifier = '+' . $this->get('number_days') . ' days';
            }

            $date = new \DateTime($dateModifier, $this->getApp()->uiPersistence->getTimezone());
        }

        if ($ceilTimeIfNotExact !== null && $dateModifier !== 'exact') {
            $date = clone $date;
            $date = $ceilTimeIfNotExact
                ? $date->setTime(23, 59, 59, 999_999)
                : $date->setTime(0, 0);
        }

        return $date;
    }

    #[\Override]
    public function getFormDisplayRules(): array
    {
        return [
            'value' => [
                'op' => ['notEmpty', 'notExactly[empty]', 'notExactly[not empty]'],
            ],
            'range' => ['op' => 'isExactly[within]'],
            'exact_date' => ['value' => 'isExactly[exact]'],
            'number_days' => [
                ['value' => 'isExactly[x_day_ago]'],
                ['value' => 'isExactly[x_day_now]'],
                ['range' => 'isExactly[x_day_before]'],
                ['range' => 'isExactly[x_day_after]'],
            ],
        ];
    }
}
