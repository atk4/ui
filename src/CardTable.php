<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Data\Model;

/**
 * Card class displays a single record data.
 *
 * IMPORTANT: Although the purpose of the "Card" component will remain the same, we do plan to
 * improve implementation of a card to to use https://semantic-ui.com/views/card.html.
 */
class CardTable extends Table
{
    protected $_bypass = false;

    public function setModel(Model $model, $columndef = null)
    {
        if ($this->_bypass) {
            return parent::setModel($model);
        }

        if (!$model->loaded()) {
            throw (new Exception('Model must be loaded'))
                ->addMoreInfo('model', $model);
        }

        $data = [];

        $ui_values = $this->issetApp() ? $this->getApp()->ui_persistence->typecastSaveRow($model, $model->get()) : $model->get();

        foreach ($model->get() as $key => $value) {
            if (!$columndef || ($columndef && in_array($key, $columndef, true))) {
                $data[] = [
                    'id' => $key,
                    'field' => $model->getField($key)->getCaption(),
                    'value' => $ui_values[$key],
                ];
            }
        }

        $this->_bypass = true;
        $mm = parent::setSource($data);
        $this->addDecorator('value', [Table\Column\Multiformat::class, function ($row, $field) use ($model) {
            $field = $model->getField($row->data['id']);
            $ret = $this->decoratorFactory(
                $field,
                $field->type === 'boolean' ? [Table\Column\Status::class,  ['positive' => [true, 'Yes'], 'negative' => [false, 'No']]] : []
            );
            if ($ret instanceof Table\Column\Money) {
                $ret->attr['all']['class'] = ['single line'];
            }

            return $ret;
        }]);
        $this->_bypass = false;

        return $mm;
    }
}
