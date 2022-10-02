<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * $bar = ProgressBar::addTo($app, [10, 'label' => 'Processing files']);.
 */
class ProgressBar extends View
{
    /** @var string|false|null Contains a text label to display under the bar. Null/false will disable the label. */
    public $label;

    public $ui = 'progress';

    public $defaultTemplate = 'progress.html';

    /**
     * Value that appears on a progress bar. Set it through constructor, e.g.
     * ProgressBar::addTo($app, [20]);.
     *
     * @var int
     */
    public $value = 0;

    /** @var int Indicates a maximum value of a progress bar. */
    public $max = 100;

    /**
     * @param array|string $label
     */
    public function __construct(int $value = 0, $label = [])
    {
        $this->value = $value;

        parent::__construct($label);
    }

    protected function renderView(): void
    {
        $this->js(true)->progress(['percent' => $this->value]);

        parent::renderView();
    }

    /**
     * Return js action for incrementing progress by one.
     *
     * @return JsExpressionable
     */
    public function jsIncrement()
    {
        return $this->js()->progress('increment');
    }

    /**
     * Return js action for setting value (client-side).
     *
     * @return JsExpressionable
     */
    public function jsValue(int $value)
    {
        return $this->js()->progress(['percent' => $value]);
    }
}
