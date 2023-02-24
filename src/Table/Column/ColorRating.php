<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Exception;
use Atk4\Ui\Table;

/**
 * Example seed:
 * [ColorRating::class, [
 *     'min' => 1,
 *     'max' => 3,
 *     'colors' => [
 *         '#FF0000',
 *         '#FFFF00',
 *         '#00FF00'
 *     ]
 * ]].
 */
class ColorRating extends Table\Column
{
    /** @var float Minimum value of the gradient. */
    public $min;
    /** @var float Maximum value of the gradient. */
    public $max;

    /** @var array Hex colors. */
    public $colors = ['#FF0000', '#00FF00'];

    /** @var bool Define if values lesser than min have no color. */
    public $lessThanMinNoColor = false;

    /** @var bool Define if values greater than max have no color. */
    public $moreThanMaxNoColor = false;

    protected function init(): void
    {
        parent::init();

        if ($this->min >= $this->max) {
            throw new Exception('Min must be lower than Max');
        }

        if (count($this->colors) < 2) {
            throw new Exception('At least 2 colors must be set');
        }
    }

    public function getTagAttributes(string $position, array $attr = []): array
    {
        $attr['style'] ??= '';
        $attr['style'] .= '{$_' . $this->shortName . '_color_rating}';

        return parent::getTagAttributes($position, $attr);
    }

    public function getDataCellHtml(Field $field = null, array $attr = []): string
    {
        if ($field === null) {
            throw new Exception('ColorRating can be used only with model field');
        }

        return $this->getTag('body', '{$' . $field->shortName . '}', $attr);
    }

    public function getHtmlTags(Model $row, ?Field $field): array
    {
        $value = $field->get($row);

        $color = $value === null ? null : $this->getColorFromValue($value);

        if ($color === null) {
            return parent::getHtmlTags($row, $field);
        }

        return [
            '_' . $this->shortName . '_color_rating' => 'background-color: ' . $color . ';',
        ];
    }

    private function getColorFromValue(float $value): ?string
    {
        if ($value < $this->min) {
            if ($this->lessThanMinNoColor) {
                return null;
            }

            $value = $this->min;
        }

        if ($value > $this->max) {
            if ($this->moreThanMaxNoColor) {
                return null;
            }

            $value = $this->max;
        }

        $colorIndex = (count($this->colors) - 1) * ($value - $this->min) / ($this->max - $this->min);

        $color = $this->interpolateColor(
            $this->colors[floor($colorIndex)],
            $this->colors[ceil($colorIndex)],
            $colorIndex - floor($colorIndex)
        );

        return $color;
    }

    protected function interpolateColor(string $hexFrom, string $hexTo, float $value): string
    {
        $hexFrom = ltrim($hexFrom, '#');
        $hexTo = ltrim($hexTo, '#');

        $fromRgb = [
            'r' => hexdec(substr($hexFrom, 0, 2)),
            'g' => hexdec(substr($hexFrom, 2, 2)),
            'b' => hexdec(substr($hexFrom, 4, 2)),
        ];

        $toRgb = [
            'r' => hexdec(substr($hexTo, 0, 2)),
            'g' => hexdec(substr($hexTo, 2, 2)),
            'b' => hexdec(substr($hexTo, 4, 2)),
        ];

        $rgb = [
            'r' => round($fromRgb['r'] + $value * ($toRgb['r'] - $fromRgb['r'])),
            'g' => round($fromRgb['g'] + $value * ($toRgb['g'] - $fromRgb['g'])),
            'b' => round($fromRgb['b'] + $value * ($toRgb['b'] - $fromRgb['b'])),
        ];

        return '#' . sprintf('%02x%02x%02x', $rgb['r'], $rgb['g'], $rgb['b']);
    }
}
