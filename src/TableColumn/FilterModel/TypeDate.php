<?php

namespace atk4\ui\TableColumn\FilterModel;

use DateTime;
class TypeDate extends Generic
{
    public function init()
    {
        parent::init();

        $this->op->values = [
            'is'        => 'Is',
            'within'    => 'Is within',
            'before'    => 'Is before',
            'after'     => 'Is after',
            'on before' => 'Is on or before',
            'on after'  => 'Is on or after',
            'not'       => 'Is not',
            'empty'     => 'Is empty',
            'not empty' => 'Is not empty'
        ];

        // the date value to operate on.
        $this->value->values = [
            'today'          => 'Today',
            'tomorrow'       => 'Tomorrow',
            'yesterday'      => 'Yesterday',
            '-1 week'        => 'One week ago',
            '+1 week'        => 'One week from now',
            '-1 month'       => 'One month ago',
            '+1 month'       => 'One month from now',
            'x day ago'      => 'Numbers of days ago',
            'x day from now' => 'Number of days from now',
            'exact'          => 'Exact date'
        ];

        // The range value field use when within is select.
        $this->addField('range',
                        ['ui'=>['caption' => ''],
                         'values' => [
                             '-1 week' => 'The past week',
                             '-1 month' => 'The past month',
                             '-1 year'  => 'The past year',
                             '+1 week'  => 'The next week',
                             '+1 month' => 'The next month',
                             '+1 year'  => 'The next year',
                             'x day before' => 'The next numbers of days before',
                             'x day after'  => 'The next number of days after'
                         ]]);

        // The exact date field input when exact is select as input value.
        $this->addField('exact_date', ['type' => 'date', 'ui' => ['caption' => '']]);

        // The integer field to generate a date when x day selector is used.
        $this->addField('number_days', ['ui' => ['caption' => '', 'form' => ['Line', 'inputType' => 'number']]]);
    }

    /**
     * Set model condition base on filter value.
     *
     * @param $m
     *
     * @return mixed
     * @throws \atk4\data\Exception
     */
    public function setConditionForModel($m)
    {
        $filter = $this->tryLoadAny()->get();
        if (isset($filter['op'])) {
            switch ($filter['op']) {
                case 'is':
                    $m->addCondition($filter['name'], $this->getDate($filter['value']));
                    break;
                case 'before':
                    $m->addCondition($filter['name'], '<', $this->getDate($filter['value']));
                    break;
                case 'after':
                    $m->addCondition($filter['name'], '>', $this->getDate($filter['value']));
                    break;
                case 'on before':
                    $m->addCondition($filter['name'], '<=', $this->getDate($filter['value']));
                    break;
                case 'on after':
                    $m->addCondition($filter['name'], '>=', $this->getDate($filter['value']));
                    break;
                case 'not':
                    $m->addCondition($filter['name'], '!=', $this->getDate($filter['value']));
                    break;
                case 'empty':
                    $m->addCondition($filter['name'], '=', null);
                    break;
                case 'not empty':
                    $m->addCondition($filter['name'], '!=', null);
                    break;
                case 'within':
                    $d1 = $this->getDate($filter['value']);
                    $d2 = $this->getDate($filter['range']);
                    if ($d2 >= $d1) {
                        $value = $m->persistence->typecastSaveField($m->getElement($filter['name']), $d1);
                        $value2 = $m->persistence->typecastSaveField($m->getElement($filter['name']), $d2);
                    } else {
                        $value = $m->persistence->typecastSaveField($m->getElement($filter['name']), $d2);
                        $value2 = $m->persistence->typecastSaveField($m->getElement($filter['name']), $d1);
                    }
                    $m->addCondition($m->expr('[field] between [value] and [value2]', ['field' => $m->getElement($filter['name']), 'value' => $value, 'value2' => $value2]));
                    break;
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
    public function getDate($dateModifier)
    {
        switch ($dateModifier) {
            case 'exact':
                $date = $this->data['exact_date'];
                break;
            case 'x day ago':
            case 'x day before':
                $date = new DateTime('-'.$this->data['number_days'].' days');
                break;
            case 'x day from now':
            case 'x day after':
                $date = new DateTime('+'.$this->data['number_days'].' days');
                break;
            default:
                $date = new DateTime($dateModifier);
                break;
        }

        return $date;
    }
}