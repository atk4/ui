<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column\FilterModel;

use Atk4\Data\Model;
use Atk4\Ui\Table\Column;

class TypeBoolean extends Column\FilterModel
{
    public $noValueField = true;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->op->type = 'smallint';
        $this->op->values = [
            0 => 'Is No',
            1 => 'Is Yes',
        ];
        $this->op->default = 1;
    }

    #[\Override]
    public function setConditionForModel(Model $model): void
    {
        $filter = $this->recallData();
        if ($filter !== null) {
            $model->addCondition($this->lookupField, $filter['op'] === 1);
        }
    }
}
