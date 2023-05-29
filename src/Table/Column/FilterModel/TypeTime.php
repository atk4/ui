<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column\FilterModel;

use Atk4\Data\Model;
use Atk4\Ui\Table\Column;

class TypeTime extends Column\FilterModel
{
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

    public function setConditionForModel(Model $model)
    {
        $filter = $this->recallData();
        if ($filter !== null) {
            switch ($filter['op']) {
                case 'between':
                    $d1 = $filter['value'];
                    $d2 = $filter['range'];
                    if ($d2 >= $d1) {
                        $value = $model->getPersistence()->typecastSaveField($model->getField($filter['name']), $d1);
                        $value2 = $model->getPersistence()->typecastSaveField($model->getField($filter['name']), $d2);
                    } else {
                        $value = $model->getPersistence()->typecastSaveField($model->getField($filter['name']), $d2);
                        $value2 = $model->getPersistence()->typecastSaveField($model->getField($filter['name']), $d1);
                    }
                    $model->addCondition($model->expr('[field] between [value] and [value2]', [
                        'field' => $model->getField($filter['name']),
                        'value' => $value,
                        'value2' => $value2,
                    ]));

                    break;
                default:
                    $model->addCondition($filter['name'], $filter['op'], $filter['value']);
            }
        }

        return $model;
    }

    public function getFormDisplayRules(): array
    {
        return [
            'range' => ['op' => 'isExactly[between]'],
        ];
    }
}
