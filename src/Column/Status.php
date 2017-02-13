<?php

namespace atk4\ui\Column;

/**
 * Implements Column helper for grid.
 */
class Status extends Generic
{
    public $states = [];

    /**
     * Pass argument with possible states like this:
     *
     *  [ 'positive'=>['Paid', 'Archived'], 'negative'=>['Overdue'] ]
     */
    function __construct($states) {
        $this->states = $states;
    }

    public function getCellTemplate(\atk4\data\Field $f)
    {
        return $this->app->getTag('td', ['class'=>'{$_'.$f->short_name.'_status}'], '{$'.$f->short_name.'}');
    }

    function getHTMLTags($row, $field)
    {
        $cl = '';

        // search for a class
        foreach($this->states as $class=>$values)
        {
            if(in_array($field->get(), $values)) {
                $cl = $class;
                break;
            }
        }

        if(!$cl) return [];

        return ['_'.$field->short_name.'_status' => $cl];
    }
}
