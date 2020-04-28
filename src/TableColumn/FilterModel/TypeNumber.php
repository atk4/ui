<?php

namespace atk4\ui\TableColumn\FilterModel;

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

    public function setConditionForModel($m)
    {
        $filter = $this->tryLoadAny()->get();
        if (isset($filter['id'])) {
            switch ($filter['op']) {
                case 'between':
                    $m->addCondition(
                        $m->expr('[field] between [value] and [range]', ['field' => $m->getField($filter['name']), 'value' => $filter['value'], 'range' => $filter['range']])
                    );

                    break;
                default:
                    $m->addCondition($filter['name'], $filter['op'], $filter['value']);
            }
        }

        return $m;
    }

    public function getFormDisplayRules()
    {
        return [
            'range' => ['op' => 'isExactly[between]'],
        ];
    }
}
