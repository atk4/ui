<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column\FilterModel;

use Atk4\Data\Model;
use Atk4\Ui\Table\Column;

class TypeString extends Column\FilterModel
{
    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->op->values = [
            'is' => 'Is',
            'is not' => 'Is Not',
            'contains' => 'Contains',
            'start' => 'Start with',
            'end' => 'End with',
        ];
        $this->op->default = 'is';
    }

    #[\Override]
    public function setConditionForModel(Model $model): void
    {
        $filter = $this->recallData();
        if ($filter !== null) {
            switch ($filter['op']) {
                case 'is':
                    $model->addCondition($this->lookupField, $filter['value']);

                    break;
                case 'is not':
                    $model->addCondition($this->lookupField, '!=', $filter['value']);

                    break;
                case 'contains':
                    $model->addCondition($this->lookupField, 'like', '%' . $filter['value'] . '%');

                    break;
                case 'start':
                    $model->addCondition($this->lookupField, 'like', $filter['value'] . '%');

                    break;
                case 'end':
                    $model->addCondition($this->lookupField, 'like', '%' . $filter['value']);

                    break;
            }
        }
    }
}
