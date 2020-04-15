<?php

namespace atk4\ui\TableColumn\FilterModel;

class TypeString extends Generic
{
    public function init(): void
    {
        parent::init();

        $this->op->values = ['is' => 'Is', 'contains' => 'Contains', 'start' => 'Start with', 'end' => 'End with'];
        $this->op->default = 'is';
    }

    public function setConditionForModel($model)
    {
        $filter = $this->tryLoadAny()->get();
        if (isset($filter['id'])) {
            switch ($filter['op']) {
                case 'is':
                    $model->addCondition($filter['name'], $filter['value']);
                    break;
                case 'is not':
                    $model->addCondition($filter['name'], '!=', $filter['value']);
                    break;
                case 'contains':
                    $model->addCondition($filter['name'], 'LIKE', '%' . $filter['value'] . '%');
                    break;
                case 'start':
                    $model->addCondition($filter['name'], 'LIKE', $filter['value'] . '%');
                    break;
                case 'end':
                    $model->addCondition($filter['name'], 'LIKE', '%' . $filter['value']);
                    break;
            }
        }

        return $model;
    }
}
