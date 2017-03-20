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

        $this->cb = $this->view->add(new CallbackLater());
        $this->cb->set(function () {
            $this->view->app->terminate($this->view->render());
        });
    }

    public function jsRender()
    {
        $final = (new jQuery($this->view))
          ->spinner([
            'loaderText' => '',
            'active'     => true,
            'inline'     => true,
            'centered'   => true,
            'replace'    => true,
          ])
          ->reloadView(
          [
            'callback' => $this->cb->getURL(),
          ]
        );

        return $final->jsRender();
    }
}
