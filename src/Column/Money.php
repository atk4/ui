<?php

namespace atk4\ui\Column;

/**
 * Formatting money.
 */
class Money extends Generic
{
    public $currency = null;

    public $format = null;

    /**
     * Provided with a field definition (from a model) will return a header
     * cell, fully formatted to be included in a Grid. (<th>).
     *
     * Potentialy may include elements for sorting.
     */
    public function getHeaderCell(\atk4\data\Field $f)
    {
        $this->app->getTag('th', [], $f->getCaption());
    }

    /**
     * Provided with a field definition will return a string containing a "Template"
     * that would procude <td> cell when rendered. Example output:.
     *
     *   <td><b>{$name}</b></td>
     *
     * The must correspond to the name of the field, although you can also use multiple tags. The tag
     * will also be formatted before inserting, see UI Persistence formatting in the documentation.
     *
     * This method will be executed only once per grid rendering, if you need to format data manually,
     * you should use $this->grid->addHook('formatRow');
     */
    public function getCellTemplate(\atk4\data\Field $f)
    {
        return $val;
    }
}
