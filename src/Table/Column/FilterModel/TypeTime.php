<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column\FilterModel;

use Atk4\Data\Model;
use Atk4\Ui\Table\Column;

class TypeTime extends Column\FilterModel
{
    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->op->values = [
            '=' => '=',
            '!=' => '!=',
            '<' => '<',
            '<=' => '< or equal',
            '>' => '>',
            '>=' => '> or equal',
            'between' => 'Between',
        ];

        $this->op->default = '=';
        $this->value->type = 'time';
        $this->addField('range', ['ui' => ['caption' => ''], 'type' => 'time']);
    }

    #[\Override]
    public function setConditionForModel(Model $model): void
    {
        $filter = $this->recallData();
        if ($filter !== null) {
            switch ($filter['op']) {
                case 'between':
                    $d1 = $filter['value'];
                    $d2 = $filter['range'];
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
                    $model->addCondition($this->lookupField, $filter['op'], $filter['value']);
            }
        }
    }

    #[\Override]
    public function getFormDisplayRules(): array
    {
        return [
            'range' => ['op' => 'isExactly[between]'],
        ];
    }
}
