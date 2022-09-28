<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Table;

/**
 * Take the field value as string in CSV format or array of IDs and transforms into Fomantic-UI labels.
 * If model field values property is set, then will use titles instead of IDs as label text.
 *
 * from => label1,label2 | to => div.ui.label[label1] div.ui.label[label2]
 */
class Labels extends Table\Column
{
    /** @var array|null Allowed values, prioritized over Field::$values */
    public ?array $values = null;

    public function getHtmlTags(Model $row, ?Field $field): array
    {
        $values = $this->values ?? $field->values;

        $v = $field->get($row);
        $v = explode(',', $v);

        $labels = [];
        foreach ($v as $id) {
            $id = trim($id);

            // if field values is set, then use titles instead of IDs
            $id = $values[$id] ?? $id;

            if ($id !== '') {
                $labels[] = $this->getApp()->getTag('div', ['class' => 'ui label'], $id);
            }
        }

        $labels = implode('', $labels);

        return [$field->shortName => $labels];
    }
}
