<?php

namespace atk4\ui\TableColumn\FilterModel;

class TypeTime extends Generic
{
    public function init()
    {
        parent::init();

        $this->op->values = [
            '='       => '=',
            '!='      => '!=',
            '<'       => '<',
            '<='      => '< or equal',
            '>'       => '>',
            '>='      => '> or equal',
            'between' => 'Between',
        ];

        $this->op->default = '=';
        $this->value->type = 'time';
        $this->addField('range', ['ui' => ['caption' => ''], 'type' => 'time']);
    }

    public function setConditionForModel($m)
    {
        $filter = $this->tryLoadAny()->get();
        if (isset($filter['id'])) {
            switch ($filter['op']) {
                case 'between':
                    $d1 = $filter['value'];
                    $d2 = $filter['range'];
                    if ($d2 >= $d1) {
                        $value = $m->persistence->typecastSaveField($m->getElement($filter['name']), $d1);
                        $value2 = $m->persistence->typecastSaveField($m->getElement($filter['name']), $d2);
                    } else {
                        $value = $m->persistence->typecastSaveField($m->getElement($filter['name']), $d2);
                        $value2 = $m->persistence->typecastSaveField($m->getElement($filter['name']), $d1);
                    }
                    $m->addCondition($m->expr('[field] between [value] and [value2]', ['field' => $m->getElement($filter['name']), 'value' => $value, 'value2' => $value2]));
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
            'range'       => ['op' => 'isExactly[between]'],
        ];
    }
}
