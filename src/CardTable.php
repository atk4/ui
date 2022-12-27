<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Data\Model;

/**
 * Card class displays a single record data.
 *
 * IMPORTANT: Although the purpose of the "Card" component will remain the same, we do plan to
 * improve implementation of a card to to use https://fomantic-ui.com/views/card.html .
 */
class CardTable extends Table
{
    protected bool $_bypass = false;

    /**
     * @param array<int, string>|null $columns
     */
    public function setModel(Model $model, array $columns = null): void
    {
        if ($this->_bypass) {
            parent::setModel($model);

            return;
        }

        $model->assertIsLoaded();

        if ($columns === null) {
            $columns = array_keys($model->getFields('visible'));
        }

        $data = [];
        foreach (array_keys($model->get()) as $fieldName) {
            if (in_array($fieldName, $columns, true)) {
                $data[] = [
                    'id' => $fieldName,
                    'field' => $model->getField($fieldName)->getCaption(),
                    'value' => new Model\EntityFieldPair($model, $fieldName),
                ];
            }
        }

        $this->_bypass = true;

        parent::setSource($data);
        /*
        $mm = parent::setSource($data);
        $this->addDecorator('value', [Table\Column\Multiformat::class, function (Model $row) use ($model) {
            $field = $model->getField($row->getId());
            $ret = $this->decoratorFactory(
                $field,
                $field->type === 'boolean' ? [Table\Column\Status::class, ['positive' => [true, 'Yes'], 'negative' => [false, 'No']]] : []
            );
            if ($ret instanceof Table\Column\Money) {
                $ret->attr['all']['class'] = ['single line'];
            }

            return [$ret];
        }]);
        */

        $this->_bypass = false;
    }
}
