<?php

namespace atk4\ui\TableColumn\FilterModel;

class TypeNumber extends Generic
{
    public function init()
    {
        parent::init();

        $this->op->values = ['=', '!=', '<', '>', 'between'];
        $this->addField('value2', ['ui' => ['caption' => '']]);
    }

    public function setConditionForModel($m)
    {
        $filter = $this->tryLoadAny()->get();
        if (isset($filter['op'])) {
            switch ($filter['op']) {
                case 0: //'='
                $m->addCondition($filter['name'], $filter['value']);
                break;
                case 1: //'!='
                    $m->addCondition($filter['name'], '!=', $filter['value']);
                    break;
                case 2: //'<'
                    $m->addCondition($filter['name'], '<', $filter['value']);
                    break;
                case 3: //'>'
                    $m->addCondition($filter['name'], '>', $filter['value']);
                    break;
                case 4: //'between'
                    $m->addCondition($m->expr('[field] between [value] and [value2]'), ['field' => $filter['name'], 'value' => $filter['value'], 'value2' => $filter['value2']]);
            }
        }

        return $m;
    }
}
