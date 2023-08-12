<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column\FilterModel;

use Atk4\Data\Model;
use Atk4\Ui\Table\Column;

class TypeBoolean extends Column\FilterModel
{
    public $noValueField = true;

    protected function init(): void
    {
        parent::init();

        $this->op->values = [
            'true' => 'Is Yes',
            'false' => 'Is No',
        ];
        $this->op->default = 'true';
    }

    public function setConditionForModel(Model $model)
    {
        $filter = $this->recallData();
        if ($filter !== null) {
            $model->addCondition($filter['name'], $filter['op'] === 'true');
        }

        return $model;
    }
}
