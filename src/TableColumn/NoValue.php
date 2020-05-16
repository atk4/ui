<?php

namespace atk4\ui\TableColumn;

use atk4\data\Model;

/**
 * Class NoValue.
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
 */
class NoValue extends Generic
{
    /** @var string */
    public $no_value = ' --- ';

    public function getHTMLTags(Model $row, $field)
    {
        $actualValue = $field->get();

        if (empty($actualValue) || $actualValue === null) {
            return [$field->short_name => $this->no_value];
        }

        return parent::getHTMLTags($row, $field);
    }
}
