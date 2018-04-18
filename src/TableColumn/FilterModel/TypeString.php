<?php

namespace atk4\ui\TableColumn\FilterModel;

class TypeString extends Generic
{
    public function init()
    {
        parent::init();

        $this->op->values = ['Is', 'Contains', 'Start', 'End'];
    }

    public function setConditionForModel($model)
    {
        $filter = $this->tryLoadAny()->get();
        if (isset($filter['op'])) {
            switch ($filter['op']) {
                case 0: //'Is'
                    $model->addCondition($filter['name'], $filter['value']);
                    break;
                case 1: //'Contains'
                    $model->addCondition($filter['name'], 'LIKE', '%'.$filter['value'].'%');
                    break;
                case 2: //'Start'
                    $model->addCondition($filter['name'], 'LIKE', $filter['value'].'%');
                    break;
                case 3: //'End'
                    $model->addCondition($filter['name'], 'LIKE', '%'.$filter['value']);
                    break;
            }
        }

        return $model;
    }
}
