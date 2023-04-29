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
    public function setModel(Model $entity, array $columns = null): void
    {
        if ($this->_bypass) {
            parent::setModel($entity);

            return;
        }

        $entity->assertIsLoaded();

        if ($columns === null) {
            $columns = array_keys($entity->getFields('visible'));
        }

        $data = [];
        foreach ($entity->get() as $key => $value) {
            if (in_array($key, $columns, true)) {
                $data[] = [
                    'id' => $key,
                    'field' => $entity->getField($key)->getCaption(),
                    'value' => $this->getApp()->uiPersistence->typecastSaveField($entity->getField($key), $value),
                ];
            }
        }

        $this->_bypass = true;
        try {
            parent::setSource($data);
        } finally {
            $this->_bypass = false;
        }

        $this->addDecorator('value', [Table\Column\Multiformat::class, function (Model $row) use ($entity) {
            $field = $entity->getField($row->getId());
            $ret = $this->decoratorFactory(
                $field,
                $field->type === 'boolean' ? [Table\Column\Status::class, ['positive' => [true, 'Yes'], 'negative' => [false, 'No']]] : []
            );
            if ($ret instanceof Table\Column\Money) {
                $ret->attr['all']['class'] = ['single line'];
            }

            return [$ret];
        }]);
    }
}
