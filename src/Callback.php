<?php

namespace atk4\ui;

use atk4\core\AppScopeTrait;
use atk4\core\TrackableTrait;

class Callback
{
    use TrackableTrait;
    use AppScopeTrait;

    public $POST_trigger = false;

    /**
     * Executes user-specified action when call-back is triggered.
     *
     * @param callback $callback
     * @param array    $args
     *
     * @return mixed|null
     */
    public function set($callback, $args = [])
    {
        if ($this->POST_trigger) {
            if (isset($_POST[$this->name])) {
                return call_user_func_array($callback, $args);
            }
        } else {
            if (isset($_GET[$this->name])) {
                return call_user_func_array($callback, $args);
            }
        }
    }

    /**
     * Return URL that will trigger action on this call-back.
     *
     * @return string
     */
    public function getURL()
    {
        if ($this->POST_trigger) {
            return $_SERVER['REQUEST_URI'];
        }

        return $this->app->url([$this->name=>'callback']);
    }
}
