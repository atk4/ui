<?php

namespace atk4\ui\TableColumn\FilterModel;

use atk4\data\Model;

class TypeEnum extends Generic
{
    public function init()
    {
        Model ::init();
        $this->_initialized = true;
        $this->op = null;
        //$this->addField('also_checked', ['type' => 'boolean', 'ui' => ['caption' => 'heel']]);
        if ($this->lookupField->values) {
            foreach ($this->lookupField->values as $key => $value) {
                $this->addField($key, ['type' => 'boolean', 'ui' => ['caption' => $value]]);
            }
        } elseif ($this->lookupField->enum) {
            foreach ($this->lookupField->enum as $enum) {
                $this->addField($enum, ['type' => 'boolean', 'ui' => ['caption' => $enum]]);
            }
        }
    }

    public function setConditionForModel($model)
    {
        $filter = $this->tryLoadAny()->get();
        $values = [];
        foreach ($filter as $key => $isSet) {
            if ($isSet === true) {
                $values[] = $key;
            }
        }

        $model->addCondition($filter['name'], 'in', $values);

        return $model;
    }
}
