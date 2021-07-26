<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column\FilterModel;

use Atk4\Data\Model;
use Atk4\Ui\Table\Column;
use DateTime;

class TypeDate extends Column\FilterModel
{
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
        $this->addField('number_days', ['ui' => ['caption' => '', 'form' => [\Atk4\Ui\Form\Control\Line::class, 'inputType' => 'number']]]);
    }

    /**
     * Set model condition base on filter value.
     *
     * @return mixed
     */
    public function setConditionForModel($model)
    {
        $filter = $this->recallData();
        if (isset($filter['id'])) {
            switch ($filter['op']) {
                case 'empty':
                    $model->addCondition($filter['name'], '=', null);

                    break;
                case 'not empty':
                    $model->addCondition($filter['name'], '!=', null);

                    break;
                case 'within':
                    $d1 = $this->getDate($filter['value']);
                    $d2 = $this->getDate($filter['range']);
                    if ($d2 >= $d1) {
                        $value = $model->persistence->typecastSaveField($model->getField($filter['name']), $d1);
                        $value2 = $model->persistence->typecastSaveField($model->getField($filter['name']), $d2);
                    } else {
                        $value = $model->persistence->typecastSaveField($model->getField($filter['name']), $d2);
                        $value2 = $model->persistence->typecastSaveField($model->getField($filter['name']), $d1);
                    }
                    $model->addCondition($model->expr('[field] between [value] and [value2]', ['field' => $model->getField($filter['name']), 'value' => $value, 'value2' => $value2]));

                    break;
                default:
                    $model->addCondition($filter['name'], $filter['op'], $this->getDate($filter['value']));
            }
        }

        return $model;
    }

    /**
     * Get date object according to it's modifier string.
     * Will construct and return a date object base on constructor string.
     *
     * @param string $dateModifier the string to pass to generated a date from
     *
     * @return DateTime
     */
    public function getDate($dateModifier)
    {
        switch ($dateModifier) {
            case 'exact':
                $date = $this->get('exact_date');

                break;
            case 'x_day_ago':
            case 'x_day_before':
                $date = new DateTime('-' . $this->get('number_days') . ' days');

                break;
            case 'x_day_now':
            case 'x_day_after':
                $date = new DateTime('+' . $this->get('number_days') . ' days');

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
