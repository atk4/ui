<?php

namespace atk4\ui;

/**
 * Class implements ProgressBar.
 *
 * $bar = ProgressBar::addTo($app, [
 *  10,
 *  'label' => 'Processing files',
 *  ]);
 */
class ProgressBar extends View
{
    /**
     * Contains a text label to display under the bar. Null/false will disable the label.
     *
     * @var string|null|false
     */
    public $label = null;

    public $ui = 'progress';

    public $defaultTemplate = 'progress.html';

    /**
     * Value that appears on a progress bar. Set it through constructor, e.g.
     * ProgressBar::addTo($app, [20]);.
     *
     * @var int
     */
    public $value = 0;

    /**
     * Indicates a maximum value of a progress bar.
     *
     * @var int
     */
    public $max = 100;

    public function __construct($value = 0, $label = null, $class = null)
    {
        $this->value = $value;

        parent::__construct($label, $class);
    }

    public function renderView()
    {
        $this->js(true)->progress(['percent'=>$this->value]);

        return parent::renderView();
    }

    /**
     * Return js action for incrementing progress by one.
     *
     * @return jsExpressionable
     */
    public function jsIncrement()
    {
        return $this->js()->progress('increment');
    }

    /**
     * Return js action for setting value (client-side).
     *
     * @param int $value new value
     *
     * @return jsExpressionable
     */
    public function jsValue($value)
    {
        return $this->js()->progress(['percent'=>(int) $value]);
    }
}
