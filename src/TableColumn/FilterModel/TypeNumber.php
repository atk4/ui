<?php

namespace atk4\ui\TableColumn\FilterModel;

use atk4\data\Model;

class TypeNumber extends Generic
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

        $this->value->ui['form'] = ['Line', 'inputType' => 'number'];
        $this->addField('range', ['ui' => ['caption' => '', 'form' => ['Line', 'inputType' => 'number']]]);
    }

    public function setConditionForModel(Model $model): Model
    {
        $filter = $this->tryLoadAny()->get();
        if (isset($filter['id'])) {
            switch ($filter['op']) {
                case 'between':
                    $model->addCondition(
                        $model->expr('[field] between [value] and [range]', ['field' => $model->getField($filter['name']), 'value' => $filter['value'], 'range' => $filter['range']])
                    );

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
