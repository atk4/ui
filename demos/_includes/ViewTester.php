<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsReload;
use Atk4\Ui\Label;
use Atk4\Ui\View;

/**
 * This view is designed to verify various things about it's positioning, e.g.
 * can its callbacks reach itself and potentially more.
 */
class ViewTester extends View
{
    protected function init(): void
    {
        parent::init();

        $label = Label::addTo($this, ['CallBack', 'detail' => 'fail', 'class.red' => true]);
        $reload = new JsReload($this, [$this->name => 'ok']);

        if ($this->getApp()->hasRequestQueryParam($this->name)) {
            $label->class[] = 'green';
            $label->detail = 'success';
        } else {
            $this->js(true, $reload);
            $this->js(true, new JsExpression('var s = Date.now(); var i = setInterval(function () { var p = Date.now() - s; var el = $([]); el.find(\'.detail\').text(p + \'ms\'); if (el.is(\'.green\')) { clearInterval(i); }}, 100)', [$label]));
        }
    }
}
