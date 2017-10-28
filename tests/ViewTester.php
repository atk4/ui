<?php

namespace atk4\ui\tests;

/**
 * This view is designed to verify various things about it's positioning, e.g.
 * can its callbacks reach itself and potentially more.
 */
class ViewTester extends \atk4\ui\View
{
    public function init()
    {
        parent::init();

        $label = $this->add(['Label', 'CallBack', 'detail'=>'fail', 'red']);
        $reload = new \atk4\ui\jsReload($this, [$this->name=>'ok']);

        if (isset($_GET[$this->name])) {
            $label->class[] = 'green';
            $label->detail = 'success';
        } else {
            $this->js(true, $reload);
        }
    }
}
