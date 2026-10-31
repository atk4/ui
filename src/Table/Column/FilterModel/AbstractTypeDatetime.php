<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column\FilterModel;

use Atk4\Ui\Table\Column;

abstract class AbstractTypeDatetime extends Column\FilterModel
{
    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->op->values = [
            '=' => 'Is',
            'within' => 'Is within',
            '<' => 'Is before',
            '>' => 'Is after',
            '<=' => 'Is on or before',
            '>=' => 'Is on or after',
            '!=' => 'Is not',
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
                '-1 month' => 'The past month',
                '-1 year' => 'The past year',
                '+1 week' => 'The next week',
                '+1 month' => 'The next month',
                '+1 year' => 'The next year',
                'x_day_before' => 'The next number of days before',
                'x_day_after' => 'The next number of days after',
            ],
        ]);

        // the exact date field input when exact is select as input value
        $this->addField('exact_date', ['type' => $this->lookupField->type, 'ui' => ['caption' => '']]);

        // the integer field to generate a date when x day selector is used
        $this->addField('number_days', ['type' => 'integer', 'ui' => ['caption' => '']]);
    }

    /**
     * Get date object according to it's modifier string.
     * Will construct and return a date object base on constructor string.
     *
     * @param string $dateModifier the string to pass to generated a date from
     */
    public function getDatetime(string $dateModifier): ?\DateTime
    {
        switch ($dateModifier) {
            case 'exact':
                $date = $this->get('exact_date');

                break;
            case 'x_day_ago':
            case 'x_day_before':
                $date = new \DateTime('-' . $this->get('number_days') . ' days');

                break;
            case 'x_day_now':
            case 'x_day_after':
                $date = new \DateTime('+' . $this->get('number_days') . ' days');

                break;
            default:
                $date = null;
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
