<?php
/**
 * Copyright (c) 2019.
 *
 * Francesco "Abbadon1334" Danti <fdanti@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */

namespace atk4\ui\TableColumn;

use atk4\data\Field;
use atk4\ui\Exception;

/**
 * Class KeyValue
 *
 * if field have values without a relation
 * like a status or a coded state of a process
 * Ex :
 * Machine state :
 * 0 => off
 * 1 => powerup
 * 2 => on
 * 3 => resetting
 * 4 => error
 *
 * we don't need a table to define this, cause are defined in project
 *
 * using KeyValue Column you can show this values without using DB Relations
 * need to be defined in field like this :
 *
 * $this->addField('course_payment_status', [
 * 'caption' => __('Payment Status'),
 * 'default' => 0,
 * 'values' => [
 * 0 => __('not invoiceable'),
 * 1 => __('ready to invoice'),
 * 2 => __('invoiced'),
 * 3 => __('paid'),
 * ],
 * 'ui'      => [
 * 'form' => [
 * 'DropDown'
 * ],
 * 'table' => [
 * 'KeyValue'
 * ]
 * ],
 * ]);
 *
 *
 * @package atk4\ui\TableColumn
 */
class KeyValue extends Generic
{
    public $values = [];

    public function init()
    {
        parent::init();

    }

    /**
     * @param array $row
     * @param Field $field
     *
     * @return array|void
     * @throws Exception
     */
    public function getHTMLTags($row, $field)
    {
        $values = $field->values;

        if (!is_array($values)) {
            throw new Exception('KeyValues Column need values in field definition');
            return;
        }

        if (count($values) === 0) {
            throw new Exception('KeyValues Column values must have elements');
            return;
        }

        $keyValues = $values;
        $key       = $field->get();

        $value = '';
        if (isset($keyValues[$key])) {
            $value = $keyValues[$key];
        }

        return [$field->short_name => $value];
    }
}
