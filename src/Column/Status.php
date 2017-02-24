<?php

namespace atk4\ui\Column;

/**
 * Implements Column helper for grid.
 */
class Status extends Generic
{
    public $states = [];

    /**
     * Pass argument with possible states like this:.
     *
     *  [ 'positive'=>['Paid', 'Archived'], 'negative'=>['Overdue'] ]
     */
    public function __construct($states)
    {
        $this->states = $states;
    }

    public function getCellTemplate(\atk4\data\Field $f)
    {
        return $this->app->getTag(
            'td',
            ['class'=> '{$_'.$f->short_name.'_status}'],
            $this->app->getTag('i', ['class'=>'icon {$_'.$f->short_name.'_icon}'], '').
            ' {$'.$f->short_name.'}'
        );
    }

    public function getHTMLTags($row, $field)
    {
        $cl = '';

        // search for a class
        foreach ($this->states as $class=>$values) {
            if (in_array($field->get(), $values)) {
                $cl = $class;
                break;
            }
        }

        if (!$cl) {
            return [];
        }

        switch ($cl) {
        case 'positive':
            $ic = 'checkmark';
            break;
        case 'negative':
            $ic = 'close';
            break;
        case 'default':
            $ic = 'question';
            break;

        }

        return [
            '_'.$field->short_name.'_status' => $cl.' single line',
            '_'.$field->short_name.'_icon'   => $ic,
        ];
    }
}
