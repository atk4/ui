<?php

namespace atk4\ui\TableColumn\FilterModel;

use DateTime;

class TypeDatetime extends Generic
{
    public function init(): void
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

        // the date value to operate on.
        $this->value->values = [
            'today' => 'Today',
            'tomorrow' => 'Tomorrow',
            'yesterday' => 'Yesterday',
            '-1 week' => 'One week ago',
            '+1 week' => 'One week from now',
            '-1 month' => 'One month ago',
            '+1 month' => 'One month from now',
            'x_day_ago' => 'Numbers of days ago',
            'x_day_now' => 'Number of days from now',
            'exact' => 'Exact date',
        ];

        // The range value field use when within is select.
        $this->addField('range', [
            'ui' => ['caption' => ''],
            'values' => [
                '-1 week' => 'The past week',
                '-1 month' => 'The past month',
                '-1 year' => 'The past year',
                '+1 week' => 'The next week',
                '+1 month' => 'The next month',
                '+1 year' => 'The next year',
                'x_day_before' => 'The next numbers of days before',
                'x_day_after' => 'The next number of days after',
            ],
        ]);

        // The exact date field input when exact is select as input value.
        $this->addField('exact_date', ['type' => 'date', 'ui' => ['caption' => '']]);

        // The integer field to generate a date when x day selector is used.
        $this->addField('number_days', ['ui' => ['caption' => '', 'form' => ['Line', 'inputType' => 'number']]]);
    }

    /**
     * Set model condition base on filter value.
     *
     * @throws \atk4\data\Exception
     *
     * @return mixed
     */
    public function setConditionForModel($m)
    {
        $filter = $this->tryLoadAny()->get();
        if (isset($filter['id'])) {
            switch ($filter['op']) {
                case 'empty':
                    $m->addCondition($filter['name'], '=', null);

                    break;
                case 'not empty':
                    $m->addCondition($filter['name'], '!=', null);

                    break;
                case 'within':
                    $d1 = $this->getDatetime($filter['value'])->setTime(0, 0, 0);
                    $d2 = $this->getDatetime($filter['range'])->setTime(23, 59, 59);
                    if ($d2 >= $d1) {
                        $value = $m->persistence->typecastSaveField($m->getField($filter['name']), $d1);
                        $value2 = $m->persistence->typecastSaveField($m->getField($filter['name']), $d2);
                    } else {
                        $value = $m->persistence->typecastSaveField($m->getField($filter['name']), $d2);
                        $value2 = $m->persistence->typecastSaveField($m->getField($filter['name']), $d1);
                    }
                    $m->addCondition($m->expr('[field] between [value] and [value2]', ['field' => $m->getField($filter['name']), 'value' => $value, 'value2' => $value2]));

                    break;
                case '!=':
                case '=':
                    $d1 = clone $this->getDatetime($filter['value'])->setTime(0, 0, 0);
                    $d2 = $this->getDatetime($filter['value'])->setTime(23, 59, 59);
                    if ($d2 >= $d1) {
                        $value = $m->persistence->typecastSaveField($m->getField($filter['name']), $d1);
                        $value2 = $m->persistence->typecastSaveField($m->getField($filter['name']), $d2);
                    } else {
                        $value = $m->persistence->typecastSaveField($m->getField($filter['name']), $d2);
                        $value2 = $m->persistence->typecastSaveField($m->getField($filter['name']), $d1);
                    }
                    $between_condition = $filter['op'] == '!=' ? 'not between' : 'between';
                    $m->addCondition($m->expr("[field] {$between_condition} [value] and [value2]", ['field' => $m->getField($filter['name']), 'value' => $value, 'value2' => $value2]));

                    break;
                case '>':
                case '<=':
                    $m->addCondition($filter['name'], $filter['op'], $this->getDatetime($filter['value'])->setTime(23, 59, 59));

                    break;
                case '<':
                case '>=':
                    $m->addCondition($filter['name'], $filter['op'], $this->getDatetime($filter['value'])->setTime(0, 0, 0));

                    break;
                default:
                    $m->addCondition($filter['name'], $filter['op'], $this->getDatetime($filter['value']));
            }
        }

        return $m;
    }

    /**
     * Get date object according to it's modifier string.
     * Will construct and return a date object base on constructor string.
     *
     * @param string $dateModifier The string to pass to generated a date from.
     *
     * @return DateTime
     */
    public function getDatetime($dateModifier)
    {
        switch ($dateModifier) {
            case 'exact':
                $date = $this->data['exact_date'];

                break;
            case 'x_day_ago':
            case 'x_day_before':
                $date = new DateTime('-' . $this->data['number_days'] . ' days');

                break;
            case 'x_day_now':
            case 'x_day_after':
                $date = new DateTime('+' . $this->data['number_days'] . ' days');

                break;
            default:
                $date = new DateTime($dateModifier);

                break;
        }

        return $date;
    }

    public function getFormDisplayRules()
    {
        return [
            'range' => ['op' => 'isExactly[within]'],
            'exact_date' => ['value' => 'isExactly[exact]'],
            'number_days' => [['value' => 'isExactly[x_day_ago]'], ['value' => 'isExactly[x_day_now]']],
        ];
    }
}
