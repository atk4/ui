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

    /**
     * @param array<int, string>|null $columns
     *
     * @return Model
     */
    public function setModel(Model $model, array $columns = null)
    {
        if ($this->_bypass) {
            return parent::setModel($model);
        }

        if (!$model->loaded()) {
            throw (new Exception('Model must be loaded'))
                ->addMoreInfo('model', $model);
        }

        $data = [];

        $uiValues = $this->getApp()->ui_persistence->typecastSaveRow($model, $model->get());

        foreach ($model->get() as $key => $value) {
            if ($columns === null || in_array($key, $columns, true)) {
                $data[] = [
                    'id' => $key,
                    'field' => $model->getField($key)->getCaption(),
                    'value' => $uiValues[$key],
                ];
            }
        }

        $this->_bypass = true;
        $mm = parent::setSource($data);
        $this->addDecorator('value', [Table\Column\Multiformat::class, function (Model $row, $field) use ($model) {
            $field = $model->getField($row->getId());
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
