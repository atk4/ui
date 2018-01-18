<?php

namespace atk4\ui;

/**
 * Class implements ProgressBar.
 *
 * $bar = $app->add([
 *  'ProgressBar',
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

    public $value = 0;

    /**
     * Indicates a maximum value of a progress bar.
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

    public function jsIncrement()
    {
        return $this->js()->progress('increment');
    }

    public function jsValue($value)
    {
        return $this->js()->progress(['percent'=>(int) $value]);
    }
}
