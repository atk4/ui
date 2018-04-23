<?php

namespace atk4\ui\TableColumn\FilterModel;

class TypeNumber extends Generic
{
    public function init()
    {
        parent::init();

        $this->op->values = ['equal' => '=', 'not equal' => '!=', 'smaller' => '<', 'greater' => '>', 'between' => 'Between'];
        $this->value->ui['form'] = ['Line', 'inputType' => 'number'];
        $this->addField('value2', ['ui' => ['caption' => '', 'form' => ['Line', 'inputType' => 'number']]]);
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
                    $m->addCondition(
                        $m->expr('[field] between [value] and [value2]', ['field' => $m->getElement($filter['name']), 'value' => $filter['value'], 'value2' => $filter['value2']])
                    );
            }
        }

        return $m;
    }
}
