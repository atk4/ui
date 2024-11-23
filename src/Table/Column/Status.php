<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Exception;
use Atk4\Ui\Table;

/**
 * Implements Column helper for grid.
 */
class Status extends Table\Column
{
    /** @var array<string, list<string>> Describes list of highlighted statuses for this Field. */
    public array $states = [];

    /**
     * Pass argument with possible states like this:.
     *
     *  ['positive' => ['Paid', 'Archived'], 'negative' => ['Overdue']]
     *
     * @param array<string, list<string>> $states List of status => [value, value, value]
     */
    public function __construct(array $states)
    {
        parent::__construct();

        $this->states = $states;
    }

    #[\Override]
    public function getDataCellHtml(?Field $field = null, array $attr = []): string
    {
        if ($field === null) {
            throw new Exception('Status can be used only with model field');
        }

        $attr = $this->mergeTagAttributes(
            $this->getTagAttributes('body'),
            $attr,
            ['class' => ['{$_' . $field->shortName . '_status}']],
        );
        $attr['class'] = implode(' ', $attr['class']);

        return $this->getApp()->getTag('td', $attr, [
            ['i', ['class' => 'icon {$_' . $field->shortName . '_icon}'], ''],
            ' {$' . $field->shortName . '}',
        ]);
    }

    #[\Override]
    public function getHtmlTags(Model $row, ?Field $field): array
    {
        $cl = '';

        // search for a class
        foreach ($this->states as $class => $values) {
            if (in_array($field->get($row), $values, true)) {
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
            '_' . $field->shortName . '_status' => $cl . ' single line',
            '_' . $field->shortName . '_icon' => $ic,
        ];
    }
}
