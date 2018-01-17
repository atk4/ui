<?php

namespace atk4\ui;

/**
 * This class generates action, that will be able to loop-back to the callback method.
 */
class jsReload implements jsExpressionable
{
    public $view = null;

    public $cb = null;

    /**
     * If defined, they will be added at the end of your URL.
     * Value in ARG can be either string or jsExpressionable.
     */
    public $args = [];

    public function __construct($view, $args = [])
    {
        $this->view = $view;

        $this->args = $args;
    }
    /*

        $this->cb = $this->view->_add(new CallbackLater());
        $this->cb->set(function () {
            $this->view->app->terminate($this->view->renderJSON());
        });
    }
     */

    public function jsRender()
    {
        $final = (new jQuery($this->view))
          ->atkReloadView(
          [
              'uri'         => $this->view->url(['__atk_reload'=>$this->view->name]),
              'uri_options' => $this->args,
          ]
        );

        return $final->jsRender();
    }
}
