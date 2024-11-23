<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column\FilterModel;

use Atk4\Data\Model;
use Atk4\Ui\Table\Column;

class TypeEnum extends Column\FilterModel
{
    #[\Override]
    protected function init(): void
    {
        // bypass parent init since we are not using op and value field but create them from the lookup field value

        Model::init();
        $this->afterInit();

        $this->op = null;
        if ($this->lookupField->values !== null) {
            foreach ($this->lookupField->values as $key => $value) {
                $this->addField($key, ['type' => 'boolean', 'ui' => ['caption' => $value]]);
            }
        } elseif ($this->lookupField->enum !== null) {
            foreach ($this->lookupField->enum as $enum) {
                $this->addField($enum, ['type' => 'boolean', 'ui' => ['caption' => $enum]]);
            }
        }
    }

    #[\Override]
    public function setConditionForModel(Model $model): void
    {
        $filter = $this->recallData();
        if ($filter !== null) {
            $values = [];
            foreach ($filter as $key => $isSet) {
                if ($isSet === true) {
                    $values[] = $key;
                }
            }
            if ($values !== []) {
                $model->addCondition($this->lookupField, 'in', $values);
            }
        }
    }
}
