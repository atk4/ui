<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/**
 * This view is designed to verify various things about it's positioning, e.g.
 * can its callbacks reach itself and potentially more.
 */
class ViewTester extends \Atk4\Ui\View
{
    protected function init(): void
    {
        parent::init();

        $label = \Atk4\Ui\Label::addTo($this, ['CallBack', 'detail' => 'fail', 'red']);
        $reload = new \Atk4\Ui\JsReload($this, [$this->name => 'ok']);

        if (isset($_GET[$this->name])) {
            $label->class[] = 'green';
            $label->detail = 'success';
        } else {
            $this->js(true, $reload);
            $this->js(true, new \Atk4\Ui\JsExpression('var s = Date.now(); var i=setInterval(function() { var p = Date.now()-s; var el=$[]; el.find(".detail").text(p+"ms"); if(el.is(".green")) { clearInterval(i); }}, 100)', [$label]));
        }
    }
}
