<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column\FilterModel;

use Atk4\Data\Model;

class TypeDatetime extends TypeDate
{
    #[\Override]
    public function setConditionForModel(Model $model): void
    {
        $filter = $this->recallData();
        if ($filter === null || $filter['op'] === null) {
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
                $d1 = $this->getDateTime($filter['value'])->setTime(0, 0, 0);
                $d2 = $this->getDateTime($filter['range'])->setTime(23, 59, 59, 999_999);
                if ($d1 > $d2) {
                    [$d1, $d2] = [$d2, $d1];
                }
                $model->addCondition($model->expr('[field] between [value] and [value2]', [
                    'field' => $this->lookupField,
                    'value' => $model->getPersistence()->typecastSaveField($this->lookupField, $d1),
                    'value2' => $model->getPersistence()->typecastSaveField($this->lookupField, $d2),
                ]));

                break;
            case '!=':
            case '=':
                $d1 = clone $this->getDateTime($filter['value'])->setTime(0, 0, 0);
                $d2 = $this->getDateTime($filter['value'])->setTime(23, 59, 59, 999_999);
                if ($d1 > $d2) {
                    [$d1, $d2] = [$d2, $d1];
                }
                $betweenOperator = $filter['op'] === '!=' ? 'not between' : 'between';
                $model->addCondition($model->expr('[field] ' . $betweenOperator . ' [value] and [value2]', [
                    'field' => $this->lookupField,
                    'value' => $model->getPersistence()->typecastSaveField($this->lookupField, $d1),
                    'value2' => $model->getPersistence()->typecastSaveField($this->lookupField, $d2),
                ]));

                break;
            case '>':
            case '<=':
                $model->addCondition($this->lookupField, $filter['op'], $this->getDateTime($filter['value'])->setTime(23, 59, 59, 999_999));

                break;
            case '<':
            case '>=':
                $model->addCondition($this->lookupField, $filter['op'], $this->getDateTime($filter['value'])->setTime(0, 0, 0));

                break;
            default:
                $model->addCondition($this->lookupField, $filter['op'], $this->getDateTime($filter['value']));
        }
    }
}
