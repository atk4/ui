<?php

namespace atk4\ui\TableColumn\FilterModel;

use atk4\data\Model;

class TypeTime extends Generic
{
    public function init(): void
    {
        parent::init();

        $this->op->values = [
            '=' => '=',
            '!=' => '!=',
            '<' => '<',
            '<=' => '< or equal',
            '>' => '>',
            '>=' => '> or equal',
            'between' => 'Between',
        ];

        $this->op->default = '=';
        $this->value->type = 'time';
        $this->addField('range', ['ui' => ['caption' => ''], 'type' => 'time']);
    }

    public function setConditionForModel(Model $model): Model
    {
        $filter = $this->tryLoadAny()->get();
        if (isset($filter['id'])) {
            switch ($filter['op']) {
                case 'between':
                    $d1 = $filter['value'];
                    $d2 = $filter['range'];
                    $field = $model->getField($filter['name'])
                    if ($d2 >= $d1) {
                        $value = $model->persistence->typecastSaveField($field, $d1);
                        $value2 = $model->persistence->typecastSaveField($field, $d2);
                    } else {
                        $value = $model->persistence->typecastSaveField($field, $d2);
                        $value2 = $model->persistence->typecastSaveField($field, $d1);
                    }
                    $model->addCondition($m->expr('[field] between [value] and [value2]', ['field' => $field, 'value' => $value, 'value2' => $value2]));

                    break;
                default:
                    $model->addCondition($filter['name'], $filter['op'], $filter['value']);
            }
        }

        return $model;
    }

    public function getFormDisplayRules(): array
    {
        return [
            'range' => ['op' => 'isExactly[between]'],
        ];
    }
}
