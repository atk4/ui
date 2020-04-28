<?php

namespace atk4\ui\TableColumn\FilterModel;

class TypeBoolean extends Generic
{
    public $noValueField = true;

    public function init(): void
    {
        parent::init();

        $this->op->values = ['true' => 'Is Yes', 'false' => 'Is No'];
        $this->op->default = 'true';
    }

    public function setConditionForModel($model)
    {
        $filter = $this->tryLoadAny()->get();
        if (isset($filter['id'])) {
            $model->addCondition($filter['name'], $filter['op'] === 'true');
        }

        return $model;
    }
}
