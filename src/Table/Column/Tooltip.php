<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Exception;
use Atk4\Ui\Table;

/**
 * Class Tooltip.
 *
 * column to add a little icon to show on hover a text
 * text is taken by the Row Model in $tooltip_field
 *
 * @usage   : $crud->addDecorator('paid_date',  new \Atk4\Ui\Table\Column\Tooltip('note'));
 *
 * @usage   : $crud->addDecorator('paid_date',  new \Atk4\Ui\Table\Column\Tooltip('note','error red'));
 */
class Tooltip extends Table\Column
{
    /**
     * @var string
     */
    public $icon;

    /**
     * @var string
     */
    public $tooltip_field;

    protected function init(): void
    {
        parent::init();

        if (!$this->icon) {
            $this->icon = 'info circle';
        }

        if (!$this->tooltip_field) {
            throw new Exception('Tooltip field must be defined');
        }
    }

    public function getDataCellHtml(Field $field = null, $extra_tags = [])
    {
        if ($field === null) {
            throw new Exception('Tooltip can be used only with model field');
        }

        $attr = $this->getTagAttributes('body');

        $extra_tags = array_merge_recursive($attr, $extra_tags, ['class' => '{$_' . $field->short_name . '_tooltip}']);

        if (is_array($extra_tags['class'] ?? null)) {
            $extra_tags['class'] = implode(' ', $extra_tags['class']);
        }

        return $this->getApp()->getTag('td', $extra_tags, [
            ' {$' . $field->short_name . '}' . $this->getApp()->getTag('span', [
                'class' => 'ui icon link {$_' . $field->short_name . '_data_visible_class}',
                'data-tooltip' => '{$_' . $field->short_name . '_data_tooltip}',
            ], [
                ['i', ['class' => 'ui icon {$_' . $field->short_name . '_icon}']],
            ]),
        ]);
    }

    public function getHtmlTags(Model $row, $field)
    {
        // @TODO remove popup tooltip when null
        $tooltip = $row->get($this->tooltip_field);

        if ($tooltip === null || $tooltip === '') {
            return [
                '_' . $field->short_name . '_data_visible_class' => 'transition hidden',
                '_' . $field->short_name . '_data_tooltip' => '',
                '_' . $field->short_name . '_icon' => '',
            ];
        }

        return [
            '_' . $field->short_name . '_data_visible_class' => '',
            '_' . $field->short_name . '_data_tooltip' => $tooltip,
            '_' . $field->short_name . '_icon' => $this->icon,
        ];
    }
}
