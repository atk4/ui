<?php

namespace atk4\ui\TableColumn\FilterModel;

use DateTime;

class TypeTime extends Generic
{
    public function init()
    {
        parent::init();

        $this->op->values = ['equal' => '=', 'not equal' => '!=', 'smaller' => '<', 'greater' => '>', 'between' => 'Between'];
        //$this->value->ui['form'] = ['Line', 'inputType' => 'time'];
        $this->value->type = 'time';
        $this->addField('range', ['ui' => ['caption' => ''], 'type' => 'time']);
    }

    public function setConditionForModel($m)
    {
        $filter = $this->tryLoadAny()->get();
        if (isset($filter['op'])) {
            switch ($filter['op']) {
                case 'equal':
                    $m->addCondition($filter['name'], $filter['value']);
                    break;
                case 'not equal':
                    $m->addCondition($filter['name'], '!=', $filter['value']);
                    break;
                case 'smaller':
                    $m->addCondition($filter['name'], '<', $filter['value']);
                    break;
                case 'greater':
                    $m->addCondition($filter['name'], '>', $filter['value']);
                    break;
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
            }
        }

        return $m;
    }
}
