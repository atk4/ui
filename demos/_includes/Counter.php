<?php


namespace atk4\ui\demo;

/**
 * Counter for certain demos file.
 */
class Counter extends \atk4\ui\FormField\Line
{
    public $content = 20; // default

    public function init(): void
    {
        parent::init();

        $this->actionLeft = new \atk4\ui\Button(['icon' => 'minus']);
        $this->action = new \atk4\ui\Button(['icon' => 'plus']);

        $this->actionLeft->js('click', $this->jsInput()->val(new \atk4\ui\jsExpression('parseInt([])-1', [$this->jsInput()->val()])));
        $this->action->js('click', $this->jsInput()->val(new \atk4\ui\jsExpression('parseInt([])+1', [$this->jsInput()->val()])));
    }
}
