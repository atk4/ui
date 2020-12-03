<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Model;
use Atk4\Ui\Table;

/**
 * Implements Column helper for grid.
 */
class Status extends Table\Column
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

    public function getDataCellHtml(\Atk4\Data\Field $field = null, $extra_tags = [])
    {
        if ($field === null) {
            throw new \Atk4\Ui\Exception('Status can be used only with model field');
        }

        $attr = $this->getTagAttributes('body');

        $extra_tags = array_merge_recursive($attr, $extra_tags, ['class' => '{$_' . $field->short_name . '_status}']);

        if (is_array($extra_tags['class'] ?? null)) {
            $extra_tags['class'] = implode(' ', $extra_tags['class']);
        }

        return $this->getApp()->getTag(
            'td',
            $extra_tags,
            [$this->getApp()->getTag('i', ['class' => 'icon {$_' . $field->short_name . '_icon}'], '') .
            ' {$' . $field->short_name . '}', ]
        );
    }

    public function getHtmlTags(Model $row, $field)
    {
        $cl = '';

        // search for a class
        foreach ($this->states as $class => $values) {
            if (in_array($field->get(), $values, true)) {
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
            '_' . $field->short_name . '_status' => $cl . ' single line',
            '_' . $field->short_name . '_icon' => $ic,
        ];
    }
}
