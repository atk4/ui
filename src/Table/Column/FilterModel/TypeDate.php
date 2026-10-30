<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column\FilterModel;

use Atk4\Data\Model;

class TypeDate extends AbstractTypeDatetime
{
    #[\Override]
    public function setConditionForModel(Model $model): void
    {
        $filter = $this->recallData();
        if ($filter === null) {
            return;
        }

        switch ($filter['op']) {
            case 'empty':
                $model->addCondition($this->lookupField, '=', null);

                break;
            case 'not empty':
                $model->addCondition($this->lookupField, '!=', null);

                break;
            case 'within':
                $d1 = $this->getDatetime($filter['value']);
                $d2 = $this->getDatetime($filter['range']);
                if ($d1 > $d2) {
                    [$d1, $d2] = [$d2, $d1];
                }
                $model->addCondition($model->expr('[field] between [value] and [value2]', [
                    'field' => $this->lookupField,
                    'value' => $model->getPersistence()->typecastSaveField($this->lookupField, $d1),
                    'value2' => $model->getPersistence()->typecastSaveField($this->lookupField, $d2),
                ]));

                break;
            default:
                $model->addCondition($this->lookupField, $filter['op'], $this->getDatetime($filter['value']));
        }
    }
}
