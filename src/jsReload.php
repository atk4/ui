<?php

namespace atk4\ui;

/**
 * This class generates action, that will be able to loop-back to the callback method.
 */
class jsReload implements jsExpressionable
{
    public $view = null;

    public $cb = null;

    public function __construct($view)
    {
        $this->view = $view;

        $this->cb = $this->view->add(new Callback('reload'));

        $this->cb->set(function () {
            echo $this->view->render();
            $this->view->app->run_called = true; // prevent shutdown function from triggering.
            exit;
        });
    }

    public function jsRender()
    {
        $addSpinner = (new jQuery($this->view))->text('')->append("<div class='ui active loader inline'></div>");

        $getRequest = (new jQuery())->get($this->cb->getURL(), '', new jsFunction(['data'], [
          (new jQuery($this->view))->replaceWith(new jsExpression('data')),
        ]));

        $final = new jsChain();
        $final->_constructorArgs = [$addSpinner, $getRequest];

        return $final->jsRender();
    }
}
