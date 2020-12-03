<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Model;
use Atk4\Ui\Table;

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
class NoValue extends Table\Column
{
    /** @var string */
    public $no_value = ' --- ';

    public function getHtmlTags(Model $row, $field)
    {
        $actualValue = $field->get();

        if (empty($actualValue) || $actualValue === null) {
            return [$field->short_name => $this->no_value];
        }

        return parent::getHtmlTags($row, $field);
    }
}
