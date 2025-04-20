<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Data\Model;
use Atk4\Ui\View\EntityTrait;

/**
 * Card class displays a single record data.
 *
 * IMPORTANT: Although the purpose of the "Card" component will remain the same, we do plan to
 * improve implementation of a card to to use https://fomantic-ui.com/views/card.html .
 */
class CardTable extends Table
{
    use EntityTrait;

    protected bool $_bypass = false;

    /**
     * @deprecated
     *
     * @param never $model
     */
    #[\Override] // @phpstan-ignore method.childParameterType
    public function setModel(Model $model, ?array $fields = null): void
    {
        if ($this->_bypass) {
            parent::setModel($model);

            return;
        }

        throw new Exception('Use CardTable::setEntity() method instead for entity set');
    }

    /**
     * @param list<string>|null $fields
     */
    public function setEntity(Model $entity, ?array $fields = null): void
    {
        $entity->assertIsLoaded();

        if ($fields === null) {
            $fields = array_keys($entity->getFields('visible'));
        }

        $data = [];
        foreach ($entity->get() as $key => $value) {
            if (in_array($key, $fields, true)) {
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
