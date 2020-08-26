<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/**
 * Counter for certain demos file.
 */
class Counter extends \atk4\ui\Form\Control\Line
{
    public $content = 20; // default

    protected function init(): void
    {
        parent::init();

        $this->actionLeft = new \atk4\ui\Button(['icon' => 'minus']);
        $this->action = new \atk4\ui\Button(['icon' => 'plus']);

        $this->actionLeft->js('click', $this->jsInput()->val(new \atk4\ui\JsExpression('parseInt([])-1', [$this->jsInput()->val()])));
        $this->action->js('click', $this->jsInput()->val(new \atk4\ui\JsExpression('parseInt([])+1', [$this->jsInput()->val()])));
    }
}
