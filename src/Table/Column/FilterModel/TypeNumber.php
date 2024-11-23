<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column\FilterModel;

use Atk4\Data\Model;
use Atk4\Ui\Form;
use Atk4\Ui\Table\Column;

class TypeNumber extends Column\FilterModel
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

        $this->value->type = $this->lookupField->type;
        $this->value->ui['form'] = [Form\Control\Line::class];
        $this->addField('range', ['ui' => ['caption' => '', 'form' => [Form\Control\Line::class]]]);
    }

    #[\Override]
    public function setConditionForModel(Model $model): void
    {
        $filter = $this->recallData();
        if ($filter !== null) {
            switch ($filter['op']) {
                case 'between':
                    $model->addCondition(
                        $model->expr('[field] between [value] and [value2]', [
                            'field' => $this->lookupField,
                            'value' => $model->getPersistence()->typecastSaveField($this->lookupField, $filter['value']),
                            'value2' => $model->getPersistence()->typecastSaveField($this->lookupField, $filter['range']),
                        ])
                    );

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
