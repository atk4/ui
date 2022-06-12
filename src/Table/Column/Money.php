<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Model;
use Atk4\Ui\Table;

/**
 * Column for formatting money.
 */
class Money extends Table\Column
{
    /** @var bool Should we show zero values in cells? */
    public $show_zero_values = true;

    // overrides
    public $attr = ['all' => ['class' => ['right aligned single line']]];

    public function getTagAttributes(string $position, array $attr = []): array
    {
        $attr = array_merge_recursive($attr, ['class' => ['{$_' . $this->shortName . '_class}']]);

        return parent::getTagAttributes($position, $attr);
    }

    public function getDataCellHtml(\Atk4\Data\Field $field = null, $extra_tags = [])
    {
        if (!isset($field)) {
            throw new \Atk4\Ui\Exception('Money column requires a field');
        }

        return $this->getTag(
            'body',
            '{$' . $field->shortName . '}',
            $extra_tags
        );
    }

    public function getHtmlTags(Model $row, $field)
    {
        if ($field->get($row) < 0) {
            return ['_' . $this->shortName . '_class' => 'negative'];
        } elseif (!$this->show_zero_values && (float) $field->get($row) === 0.0) {
            return ['_' . $this->shortName . '_class' => '', $field->shortName => '-'];
        }

        return ['_' . $this->shortName . '_class' => ''];
    }
}
