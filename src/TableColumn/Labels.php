<?php

namespace atk4\ui\TableColumn;

use atk4\data\Field;
use atk4\ui\Exception;

/**
 * Class Labels.
 *
 * Take the field value as string in CSV format or array of IDs and transforms into SemanticUI labels.
 * If model field values property is set, then will use titles instead of IDs as label text.
 *
 * from => label1,label2 | to => div.ui.label[label1] div.ui.label[label2]
 */
class Labels extends Generic
{
    /** @var array Array of allowed values. This have precedence over $field->values */
    public $values;

    /**
     * @param Model|array $row
     * @param Field|null  $field
     *
     * @return array|void
     */
    public function getHTMLTags($row, $field)
    {
        $values = $this->values ?? $field->values;

        $v = $field->get();
        $v = is_string($v) ? explode(',', $v) : $v;

        $labels= [];
        foreach ((Array) $v as $id) {
            $id = trim($id);

            // if field values is set, then use titles instead of IDs
            $id = $values[$id] ?? $id;

            if (!empty($id)) {
                $labels[] = $this->app->getTag('div', ['class' => 'ui label'], $id);
            }
        }

        $labels = implode('', $labels);

        return [$field->short_name => $labels];
    }
}
