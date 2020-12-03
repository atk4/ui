<?php

declare(strict_types=1);

namespace Atk4\Ui\Table\Column;

use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Ui\Exception;
use Atk4\Ui\Table;

/**
 * Class ColorRating
 * Can be defined like this :
 * [
 * ColorRating::class,
 *      [
 *      'min'     => 1,
 *      'max'     => 3,
 *      'steps'   => 3,
 *      'colors'  => [
 *          '#FF0000',
 *          '#FFFF00',
 *          '#00FF00'
 *      ]
 *   ]
 * ].
 */
class ColorRating extends Table\Column
{
    /**
     * Minimum value of the gradient.
     *
     * @var float
     */
    public $min;

    /**
     * Maximum value of the gradient.
     *
     * @var float
     */
    public $max;
    /**
     * Step to be calculated between colors, must be greater than 1.
     *
     * @var int
     */
    public $steps = 1;

    /**
     * Hex colors ['#FF0000','#00FF00'] from red to green.
     *
     * @var array
     */
    public $colors = ['#FF0000', '#00FF00'];

    /**
     * Store the generated Hex color based on the number of steps.
     *
     * @var array
     */
    protected $gradients = [];

    /**
     * Number of gradient, used internally.
     *
     * @var int
     */
    protected $gradients_count = 0;

    /**
     * Internally used to avoid calc on every call.
     *
     * @var float
     */
    protected $delta;

    /**
     * Define if values greater than max have no color.
     *
     * @var bool
     */
    public $more_than_max_no_color = false;

    /**
     * Define if values lesser than min have no color.
     *
     * @var bool
     */
    public $less_than_min_no_color = false;

    protected function init(): void
    {
        parent::init();

        // cast type of properties
        $this->min = (float) $this->min;
        $this->max = (float) $this->max;
        $this->delta = $this->max - $this->min;

        // Preconditions : min - max
        if ($this->min > $this->max) {
            throw new Exception('Min must be lower than Max');
        }

        if ($this->min === $this->max) {
            throw new Exception('Min and Max must be different');
        }

        // Preconditions : step
        if ($this->steps === 0) {
            throw new Exception('Step must be at least 1');
        }

        // Preconditions : colors
        if (count($this->colors) < 2) {
            throw new Exception('Colors must be more than 1');
        }

        // ALL OK

        // create gradients
        $this->createGradients();

        // count one time the gradients and reuse
        $this->gradients_count = count($this->gradients) - 1;
    }

    private function createGradients()
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

    private function createGradientSingle(&$gradients, $hexFrom, $hexTo, $steps)
    {
        $hexFrom = trim($hexFrom, '#');
        $hexTo = trim($hexTo, '#');

        $FromRgb['r'] = hexdec(substr($hexFrom, 0, 2));
        $FromRgb['g'] = hexdec(substr($hexFrom, 2, 2));
        $FromRgb['b'] = hexdec(substr($hexFrom, 4, 2));

        $ToRgb['r'] = hexdec(substr($hexTo, 0, 2));
        $ToRgb['g'] = hexdec(substr($hexTo, 2, 2));
        $ToRgb['b'] = hexdec(substr($hexTo, 4, 2));

        $StepRgb['r'] = ($FromRgb['r'] - $ToRgb['r']) / ($steps);
        $StepRgb['g'] = ($FromRgb['g'] - $ToRgb['g']) / ($steps);
        $StepRgb['b'] = ($FromRgb['b'] - $ToRgb['b']) / ($steps);

        for ($i = 0; $i <= $steps; ++$i) {
            $Rgb['r'] = floor($FromRgb['r'] - ($StepRgb['r'] * $i));
            $Rgb['g'] = floor($FromRgb['g'] - ($StepRgb['g'] * $i));
            $Rgb['b'] = floor($FromRgb['b'] - ($StepRgb['b'] * $i));

            $HexRgb['r'] = sprintf('%02x', ($Rgb['r']));
            $HexRgb['g'] = sprintf('%02x', ($Rgb['g']));
            $HexRgb['b'] = sprintf('%02x', ($Rgb['b']));

            $gradients[] = '#' . implode('', $HexRgb);
        }
    }

    public function getTagAttributes($position, $attr = [])
    {
        $attr['style'] = $attr['style'] ?? '';
        $attr['style'] .= '{$_' . $this->short_name . '_color_rating}';

        return parent::getTagAttributes($position, $attr);
    }

    public function getDataCellHtml(Field $field = null, $extra_tags = [])
    {
        if ($field === null) {
            throw new Exception('ColorRating can be used only with model field');
        }

        return $this->getTag('body', '{$' . $field->short_name . '}', $extra_tags);
    }

    public function getHtmlTags(Model $row, $field)
    {
        $value = $field->get();
        if ($value === null) {
            return parent::getHtmlTags($row, $field);
        }

        $color = $this->getColorFromValue($value);

        if ($color === null) {
            return parent::getHtmlTags($row, $field);
        }

        return [
            '_' . $this->short_name . '_color_rating' => 'background-color:' . $color . ';',
        ];
    }

    private function getColorFromValue(float $value)
    {
        if ($value <= $this->min) {
            return $this->less_than_min_no_color ? null : $this->gradients[0];
        }

        if ($value >= $this->max) {
            return $this->more_than_max_no_color ? null : end($this->gradients);
        }

        $refValue = ($value - $this->min) / $this->delta;
        $refIndex = $this->gradients_count * $refValue;

        $index = floor($refIndex);

        return $this->gradients[(int) $index];
    }
}
