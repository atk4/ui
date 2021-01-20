<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

class ReloadTest extends \Atk4\Ui\View
{
    protected function init(): void
    {
        parent::init();

        $label = \Atk4\Ui\Label::addTo($this, ['Testing...', 'detail' => '', 'red']);
        $reload = new \Atk4\Ui\JsReload($this, [$this->name => 'ok']);

        if (isset($_GET[$this->name])) {
            $label->class[] = 'green';
            $label->content = 'Reload success';
        } else {
            $this->js(true, $reload);
        }
    }
}
