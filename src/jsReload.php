<?php

namespace atk4\ui;

/**
 * This class generates action, that will be able to loop-back to the callback method.
 */
class jsReload implements jsExpressionable
{
    public $view = null;

    public $cb = null;

    public $url = null;

    public $arg = [];

    public function __construct($view, $arg = [])
    {
        $this->view = $view;
        $this->arg = $arg;

        $this->cb = $this->view->add(new CallbackLater());
        $this->cb->set(function () {
            $this->view->app->terminate($this->view->render());
        });
    }

    public function jsRender()
    {
        $addSpinner = (new jQuery($this->view))->text('')->append("<div class='ui active loader inline'></div>");

        $getRequest = (new jQuery())->get($this->url ?: $this->cb->getURL(), '', new jsFunction(['data'], [
          (new jQuery($this->view))->replaceWith(new jsExpression('data')),
        ]));

        $final = new jsChain();
        $final->_constructorArgs = [$addSpinner, $getRequest];

        return $final->jsRender();
    }
}
