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
 *     'steps' => 3,
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
    /** @var int Step to be calculated between colors, must be greater than 1. */
    public $steps = 1;

    /** @var array Hex colors ['#FF0000', '#00FF00'] from red to green. */
    public $colors = ['#FF0000', '#00FF00'];

    /** @var array Store the generated Hex color based on the number of steps. */
    protected $gradients = [];

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

        if ($this->steps === 0) {
            throw new Exception('Step must be at least 1');
        }

        if (count($this->colors) < 2) {
            throw new Exception('Colors must be more than 1');
        }

        $this->createGradients();
    }

    private function createGradients(): void
    {
        $colorFrom = '';

        foreach ($this->colors as $idx => $color) {
            // skip first
            if ($idx === 0) {
                $colorFrom = $color;

                continue;
            }

            // if already add remove last
            // because on first iteraction of ->createGradientSingle
            // will create a duplicate
            if (count($this->gradients) > 0) {
                array_pop($this->gradients);
            }

            $this->createGradientSingle($this->gradients, $colorFrom, $color, $this->steps + 1);
            $colorFrom = $color;
        }
    }

    private function createGradientSingle(array &$gradients, string $hexFrom, string $hexTo, int $steps): void
    {
        $hexFrom = trim($hexFrom, '#');
        $hexTo = trim($hexTo, '#');

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

        $stepRgb = [
            'r' => ($fromRgb['r'] - $toRgb['r']) / $steps,
            'g' => ($fromRgb['g'] - $toRgb['g']) / $steps,
            'b' => ($fromRgb['b'] - $toRgb['b']) / $steps,
        ];

        for ($i = 0; $i <= $steps; ++$i) {
            $rgb = [
                'r' => floor($fromRgb['r'] - $stepRgb['r'] * $i),
                'g' => floor($fromRgb['g'] - $stepRgb['g'] * $i),
                'b' => floor($fromRgb['b'] - $stepRgb['b'] * $i),
            ];

            $hexRgb = [
                'r' => sprintf('%02x', $rgb['r']),
                'g' => sprintf('%02x', $rgb['g']),
                'b' => sprintf('%02x', $rgb['b']),
            ];

            $gradients[] = '#' . implode('', $hexRgb);
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
        if ($value === null) {
            return parent::getHtmlTags($row, $field);
        }

        $color = $this->getColorFromValue($value);

        if ($color === null) {
            return parent::getHtmlTags($row, $field);
        }

        return [
            '_' . $this->shortName . '_color_rating' => 'background-color: ' . $color . ';',
        ];
    }

    private function getColorFromValue(float $value): ?string
    {
        if ($value <= $this->min) {
            return $this->lessThanMinNoColor ? null : $this->gradients[0];
        }

        if ($value >= $this->max) {
            return $this->moreThanMaxNoColor ? null : end($this->gradients);
        }

        $gradientsCount = count($this->gradients) - 1;
        $refValue = ($value - $this->min) / ($this->max - $this->min);
        $refIndex = $gradientsCount * $refValue;

        $index = (int) floor($refIndex);

        return $this->gradients[$index];
    }
}
