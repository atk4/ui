<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\JsExpression;

class Counter extends Form\Control\Line
{
    public $content = '20';

    protected function init(): void
    {
        parent::init();

        $this->actionLeft = new Button(['icon' => 'minus']);
        $this->action = new Button(['icon' => 'plus']);

        $this->actionLeft->js('click', $this->jsInput()->val(new JsExpression('parseInt([]) - 1', [$this->jsInput()->val()])));
        $this->action->js('click', $this->jsInput()->val(new JsExpression('parseInt([]) + 1', [$this->jsInput()->val()])));
    }
}
