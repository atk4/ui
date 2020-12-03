<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/**
 * Counter for certain demos file.
 */
class Counter extends \Atk4\Ui\Form\Control\Line
{
    public $content = 20; // default

    protected function init(): void
    {
        parent::init();

        $this->actionLeft = new \Atk4\Ui\Button(['icon' => 'minus']);
        $this->action = new \Atk4\Ui\Button(['icon' => 'plus']);

        $this->actionLeft->js('click', $this->jsInput()->val(new \Atk4\Ui\JsExpression('parseInt([])-1', [$this->jsInput()->val()])));
        $this->action->js('click', $this->jsInput()->val(new \Atk4\Ui\JsExpression('parseInt([])+1', [$this->jsInput()->val()])));
    }
}
