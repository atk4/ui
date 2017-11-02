<?php

namespace atk4\ui\TableColumn;

/**
 * Implements Column helper for grid.
 */
class Status extends Generic
{
    /**
     * Describes list of highlited statuses for this Field.
     *
     * @var array
     */
    public $states = [];

    /**
     * Pass argument with possible states like this:.
     *
     *  [ 'positive'=>['Paid', 'Archived'], 'negative'=>['Overdue'] ]
     *
     * @param array $states List of status=>[value,value,value]
     */
    public function __construct($states)
    {
        $this->states = $states;
    }

    public function getDataCellHTML(\atk4\data\Field $f = null, $extra_tags = [])
    {
        if ($f === null) {
            throw new Exception(['Status can be used only with model field']);
        }

        $extra_tags = array_merge_recursive($extra_tags, ['class' => '{$_'.$f->short_name.'_status}']);

        return $this->app->getTag(
            'td',
            $extra_tags,
            [$this->app->getTag('i', ['class' => 'icon {$_'.$f->short_name.'_icon}'], '').
            ' {$'.$f->short_name.'}', ]
        );
    }

    public function getHTMLTags($row, $field)
    {
        $cl = '';

        // search for a class
        foreach ($this->states as $class => $values) {
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
        default:
            $ic = '';

        }

        return [
            '_'.$field->short_name.'_status' => $cl.' single line',
            '_'.$field->short_name.'_icon'   => $ic,
        ];
    }
}
