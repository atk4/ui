<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Data\Field;
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
        foreach (array_keys($entity->get()) as $key) {
            if (in_array($key, $columns, true)) {
                $data[] = [
                    'id' => $key,
                    'field' => $entity->getField($key)->getCaption(),
                    'value' => new Model\EntityFieldPair($entity, $key),
                ];
            }
        }

        $this->_bypass = true;
        try {
            parent::setSource($data);
        } finally {
            $this->_bypass = false;
        }

        $this->addDecorator('value', [Table\Column\Multiformat::class, function (Model $row, Field $field) {
            $c = $this->decoratorFactory($field);
            if ($c instanceof Table\Column\Money) {
                $c->attr['all']['class'] = ['single line'];
            }

            return [$c];
        }]);
    }
}
