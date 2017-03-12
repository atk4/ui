<?php

namespace atk4\ui;

/**
 * This class generates action, that will be able to loop-back to the callback method
 */
class jsReload implements jsExpressionable
{
    public $view = null;

    public $cb = null;

    function __construct($view) {

        $this->view = $view;

        $this->cb = $this->view->add(new Callback('reload'));

        $this->cb->set(function() {
            echo $this->view->render();
            $this->view->app->run_called = true; // prevent shutdown function from triggering.
            exit;
        });
    }

    function jsRender() {

        // Temporarily here
        //$r = new jsExpression('document.location=[]', [$this->cb->getURL()]);

        // Works but not ideal! Proof of concept!
        $r = (new jQuery($this->view))->load($this->cb->getURL());

        return $r->jsRender();
    }
}
