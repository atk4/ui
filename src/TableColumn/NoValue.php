<?php

namespace atk4\ui\TableColumn;

/**
 * Used when we need to set the display value when is null.
 *
 * when we display values we have holes
 * with NoValue decorator we can show a display value for column null value
 *
 * @usage   :
 *
 * $this->addField('field', [
 *  [...]
 *  'ui' => [
 *          [...]
 *          'table' => [
 *              'NoValue', ' if empty display this value '
 *          ]
 *      ]
 * ]);
 */
class NoValue extends Generic
{
    /** @var string */
    public $no_value = ' --- ';

    public function getHTMLTags($row, $field)
    {
        $actualValue = $field->get();

        if (empty($actualValue) || is_null($actualValue)) {
            return [$field->short_name => $this->no_value];
        }

        return parent::getHTMLTags($row, $field);
    }
}
