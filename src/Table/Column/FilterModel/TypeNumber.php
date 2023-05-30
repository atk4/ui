<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column\FilterModel;

use Atk4\Data\Model;
use Atk4\Ui\Form;
use Atk4\Ui\Table\Column;

class TypeNumber extends Column\FilterModel
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

        $this->value->ui['form'] = [Form\Control\Line::class, 'inputType' => 'number'];
        $this->addField('range', ['ui' => ['caption' => '', 'form' => [Form\Control\Line::class, 'inputType' => 'number']]]);
    }

    public function setConditionForModel(Model $model)
    {
        $filter = $this->recallData();
        if ($filter !== null) {
            switch ($filter['op']) {
                case 'between':
                    $model->addCondition(
                        $model->expr('[field] between [value] and [range]', [
                            'field' => $model->getField($filter['name']),
                            'value' => $filter['value'],
                            'range' => $filter['range'],
                        ])
                    );

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
