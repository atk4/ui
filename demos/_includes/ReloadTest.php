<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Js\JsReload;
use Atk4\Ui\Label;
use Atk4\Ui\View;

class ReloadTest extends View
{
    protected function init(): void
    {
        parent::init();

        $label = Label::addTo($this, ['Testing...', 'detail' => '', 'class.red' => true]);
        $reload = new JsReload($this, [$this->name => 'ok']);

        if ($this->getApp()->hasRequestQueryParam($this->name)) {
            $label->class[] = 'green';
            $label->content = 'Reload success';
        } else {
            $this->js(true, $reload);
        }
    }
}
