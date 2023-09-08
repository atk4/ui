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
    /** @var array<string|int, string>|null Allowed values, prioritized over Field::$values */
    public ?array $values = null;

    public function getHtmlTags(Model $row, ?Field $field): array
    {
        $values = $this->values ?? $field->values;

        $v = $field->get($row);
        $v = explode(',', $v ?? '');

        $labelsHtml = [];
        foreach ($v as $id) {
            // if field values is set, then use titles instead of IDs
            $label = $values[$id] ?? $id;

            if ($label !== '') {
                $labelsHtml[] = $this->getApp()->getTag('div', ['class' => 'ui label'], $label);
            }
        }

        return [$field->shortName => implode('', $labelsHtml)];
    }
}
