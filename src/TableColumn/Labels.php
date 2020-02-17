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
    /**
     * @param array $row
     * @param Field $field
     *
     * @return array|void
     */
    public function getHTMLTags(array $row, Field $field)
    {
        $values = $field->get();
        $values = is_string($values) ? explode(',', $values) : $values;

        $labels= [];
        foreach ($values as $value) {
            $value = trim($value);

            // if field values is set, then use titles instead of IDs
            if ($field->values && isset($field->values[$value])) {
                $value = $field->values[$value];
            }

            if (!empty($value)) {
                $labels[] = $this->app->getTag('div', ['class' => 'ui label'], $value);
            }
        }

        $labels = implode('', $labels);

        return [$field->short_name => $labels];
    }
}
