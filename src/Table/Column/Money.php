<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Exception;
use Atk4\Ui\Table;

/**
 * Column for formatting money.
 */
class Money extends Table\Column
{
    public array $attr = ['all' => ['class' => ['right aligned single line']]];

    /** @var bool Should we show zero values in cells? */
    public $showZeroValues = true;

    public function getTagAttributes(string $position, array $attr = []): array
    {
        $attr = array_merge_recursive($attr, ['class' => ['{$_' . $this->shortName . '_class}']]);

        return parent::getTagAttributes($position, $attr);
    }

    public function getDataCellHtml(Field $field = null, array $attr = []): string
    {
        if ($field === null) {
            throw new Exception('Money column requires a field');
        }

        return $this->getTag('body', '{$' . $field->shortName . '}', $attr);
    }

    public function getHtmlTags(Model $row, ?Field $field): array
    {
        if ($field->get($row) < 0) {
            return ['_' . $this->shortName . '_class' => 'negative'];
        } elseif (!$this->showZeroValues && (float) $field->get($row) === 0.0) {
            return ['_' . $this->shortName . '_class' => '', $field->shortName => '-'];
        }

        return ['_' . $this->shortName . '_class' => ''];
    }
}
