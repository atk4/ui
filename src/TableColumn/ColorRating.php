<?php

namespace atk4\ui\TableColumn;

use atk4\data\Field;
use atk4\ui\Exception;

/**
 * Class ColorRating
 * Can be defined like this :
 * [
 * 'ColorRating',
 *      [
 *      'min'     => 1,
 *      'max'     => 3,
 *      'steps'   => 3,
 *      'colors'  => [
 *      '#FF0000',
 *      '#FFFF00',
 *      '#00FF00'
 *      ]
 *   ]
 * ].
 */
class ColorRating extends Generic
{
    /** @var float */
    public $min;
    /** @var float */
    public $max;
    /**
     * Step between colors.
     *
     * @var int
     */
    public $steps = 1;

    /** @var array */
    public $colors = [];

    public $gradients = [];

    public $gradients_count = 0;
    /**
     * @var float
     */
    private $delta;
    private $more_than_max_no_color;
    private $less_than_min_no_color;

    public function init()
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

        $FromRGB['r'] = hexdec(substr($hexFrom, 0, 2));
        $FromRGB['g'] = hexdec(substr($hexFrom, 2, 2));
        $FromRGB['b'] = hexdec(substr($hexFrom, 4, 2));

        $ToRGB['r'] = hexdec(substr($hexTo, 0, 2));
        $ToRGB['g'] = hexdec(substr($hexTo, 2, 2));
        $ToRGB['b'] = hexdec(substr($hexTo, 4, 2));

        $StepRGB['r'] = ($FromRGB['r'] - $ToRGB['r']) / ($steps);
        $StepRGB['g'] = ($FromRGB['g'] - $ToRGB['g']) / ($steps);
        $StepRGB['b'] = ($FromRGB['b'] - $ToRGB['b']) / ($steps);

        for ($i = 0; $i <= $steps; $i++) {
            $RGB['r'] = floor($FromRGB['r'] - ($StepRGB['r'] * $i));
            $RGB['g'] = floor($FromRGB['g'] - ($StepRGB['g'] * $i));
            $RGB['b'] = floor($FromRGB['b'] - ($StepRGB['b'] * $i));

            $HexRGB['r'] = sprintf('%02x', ($RGB['r']));
            $HexRGB['g'] = sprintf('%02x', ($RGB['g']));
            $HexRGB['b'] = sprintf('%02x', ($RGB['b']));

            $gradients[] = "#".implode(NULL, $HexRGB);
        }
    }

    public function getTagAttributes($position, $attr = [])
    {
        $attr['style'] = $attr['style'] ?? '';
        $attr['style'] .= '{$_'.$this->short_name.'_color_rating};';

        return parent::getTagAttributes($position, $attr);
    }

    public function getDataCellHTML(Field $f = null, $extra_tags = [])
    {
        if ($f === null) {
            throw new Exception(['ColorRating can be used only with model field']);
        }

        return $this->getTag('body', '{$'.$f->short_name.'}', $extra_tags);
    }

    public function getHTMLTags($row, $field)
    {
        $value = $field->get();
        if (is_null($value) || (int) $value < $this->min || (int) $value > $this->max) {
            return parent::getHTMLTags($row, $field);
        }

        $color = $this->getColorFromValue($value);

        return [
            '_'.$this->short_name.'_color_rating' => 'background-color:'.$color
        ];
    }

    private function getColorFromValue($value)
    {
        if ($this->less_than_min_no_color && $value < $this->min) {
            return $this->gradients[0];
        }

        if ($this->more_than_max_no_color && $value > $this->max) {
            return end($this->gradients);
        }

        $refValue = ($value - $this->min) / $this->delta;
        $refIndex = $this->gradients_count * $refValue;

        $index = (int) floor($refIndex);

        return $this->gradients[$index];
    }
}
