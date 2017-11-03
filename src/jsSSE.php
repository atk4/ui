<?php

namespace atk4\ui;

/**
 * Implements a class that can be mapped into arbitrary JavaScript expression.
 */
class jsSSE extends jsCallback
{
    // Allows us to fall-back to standard functionality of jsCallback if browser does not support SSE
    public $browserSupport = false;

    public function init()
    {
        parent::init();

        if ($this->triggered() == 'sse') {
            $this->browserSupport = true;
        }
    }

    public function send($action)
    {
        if ($this->browserSupport) {
            $ajaxec = $this->getAjaxec($action);

            // TODO: implement
            $this->sendBlock($ajaxec);
            $this->flush();
        } // else ignore event
    }

    public function terminate($ajaxec, $success = true)
    {
        if ($this->browserSupport) {

            // if !success, then log error to console
            $this->sendBlock($ajaxec);

            // no further output please
            $this->app->terminate();
        } else {
            $this->app->terminate(json_encode(['success' => $success, 'message' => 'Success', 'atkjs' => $ajaxec]));
        }
    }
}
