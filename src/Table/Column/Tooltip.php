<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Exception;
use Atk4\Ui\Table;

/**
 * Column to add a little icon to show on hover a text
 * text is taken by the Row Model in $tooltipField.
 *
 * $crud->addDecorator('paid_date', new Table\Column\Tooltip('note'));
 * $crud->addDecorator('paid_date', new Table\Column\Tooltip('note', 'error red'));
 */
class Tooltip extends Table\Column
{
    /** @var string */
    public $icon;

    public string $tooltipField;

    protected function init(): void
    {
        parent::init();

        if (!$this->icon) {
            $this->icon = 'info circle';
        }
    }

    public function getDataCellHtml(Field $field = null, array $attr = []): string
    {
        if ($field === null) {
            throw new Exception('Tooltip can be used only with model field');
        }

        $bodyAttr = $this->getTagAttributes('body');

        $attr = array_merge_recursive($bodyAttr, $attr, ['class' => '{$_' . $field->shortName . '_tooltip}']);

        if (is_array($attr['class'] ?? null)) {
            $attr['class'] = implode(' ', $attr['class']);
        }

        return $this->getApp()->getTag('td', $attr, [
            ' {$' . $field->shortName . '}',
            ['span', [
                'class' => 'ui icon link {$_' . $field->shortName . '_data_visible_class}',
                'data-tooltip' => '{$_' . $field->shortName . '_data_tooltip}',
            ], [
                ['i', ['class' => 'ui icon {$_' . $field->shortName . '_icon}']],
            ]],
        ]);
    }

    public function getHtmlTags(Model $row, ?Field $field): array
    {
        // @TODO remove popup tooltip when null
        $tooltip = $row->get($this->tooltipField);

        if ($tooltip === null || $tooltip === '') {
            return [
                '_' . $field->shortName . '_data_visible_class' => 'transition hidden',
                '_' . $field->shortName . '_data_tooltip' => '',
                '_' . $field->shortName . '_icon' => '',
            ];
        }

        return [
            '_' . $field->shortName . '_data_visible_class' => '',
            '_' . $field->shortName . '_data_tooltip' => $tooltip,
            '_' . $field->shortName . '_icon' => $this->icon,
        ];
    }
}
