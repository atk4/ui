<?php

namespace atk4\ui\TableColumn;

/**
 * Class NoValue
 *
 * sometime we need null values in db
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
 *
 *
 * @package atk4\ui\TableColumn
 */
class NoValue extends Generic
{
    public $no_value = NULL;

    public function __construct($no_value = NULL)
    {
        $this->no_value = $no_value;
    }

    public function getHTMLTags($row, $field)
    {
        $actualValue = $field->get();

        if (empty($actualValue) || is_null($actualValue)) {
            return [$field->short_name => $this->no_value];
        }

        return parent::getHTMLTags($row, $field);
    }
}
