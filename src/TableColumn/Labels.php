<?php

namespace atk4\ui\TableColumn;

/**
 * Class Labels
 *
 * take the fieldValue separated by commas and transforms into SemanticUI labels
 *
 * from => label1,label2 | to => div.ui.label[label1] div.ui.label[label2]
 *
 * @package atk4\ui\TableColumn
 */
class Labels extends Generic
{
    public function getHTMLTags($row, $field)
    {
        $values = explode(',', $field->get());

        $processed = [];
        foreach ($values as $value) {
            $value = trim($value);

            if (!empty($value)) {
                $processed[] = $this->app->getTag('div', ['class' => "ui label"], $value);
            }
        }

        $processed = implode('', $processed);

        return [$field->short_name => $processed];
    }
}
