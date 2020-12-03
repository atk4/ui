<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Table;

/**
 * Class Labels.
 *
 * Take the field value as string in CSV format or array of IDs and transforms into SemanticUI labels.
 * If model field values property is set, then will use titles instead of IDs as label text.
 *
 * from => label1,label2 | to => div.ui.label[label1] div.ui.label[label2]
 */
class Labels extends Table\Column
{
    /** @var array Array of allowed values. This have precedence over->values */
    public $values;

    /**
     * @param Field|null $field
     *
     * @return array|void
     */
    public function getHtmlTags(Model $row, $field)
    {
        $values = $this->values ?? $field->values;

        $v = $field->get();
        $v = is_string($v) ? explode(',', $v) : $v;

        $labels = [];
        foreach ((array) $v as $id) {
            $id = trim($id);

            // if field values is set, then use titles instead of IDs
            $id = $values[$id] ?? $id;

            if (!empty($id)) {
                $labels[] = $this->getApp()->getTag('div', ['class' => 'ui label'], $id);
            }
        }

        $labels = implode('', $labels);

        return [$field->short_name => $labels];
    }
}
