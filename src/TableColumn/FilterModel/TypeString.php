<?php

namespace atk4\ui\TableColumn\FilterModel;

class TypeString extends Generic
{
    public function init()
    {
        parent::init();

        $this->op->values = ['is' => 'Is', 'contains' => 'Contains', 'start' => 'Start', 'end' => 'End'];
    }

    public function setConditionForModel($model)
    {
        $filter = $this->tryLoadAny()->get();
        if (isset($filter['op'])) {
            switch ($filter['op']) {
                case 'is':
                    $model->addCondition($filter['name'], $filter['value']);
                    break;
                case 'contains':
                    $model->addCondition($filter['name'], 'LIKE', '%'.$filter['value'].'%');
                    break;
                case 'start':
                    $model->addCondition($filter['name'], 'LIKE', $filter['value'].'%');
                    break;
                case 'end':
                    $model->addCondition($filter['name'], 'LIKE', '%'.$filter['value']);
                    break;
            }
        }

        return $model;
    }
}
