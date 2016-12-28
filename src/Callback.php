<?php

namespace atk4\ui;


use atk4\core\AppScopeTrait;
use atk4\core\TrackableTrait;

class Callback
{
    use TrackableTrait;
    use AppScopeTrait;

    /**
     * Executes user-specified action when call-back is triggerde
     *
     * @param callback $callback
     *
     * @return mixed|null
     */
    public function set($callback, $args = [])
    {
        if (isset($_GET[$this->name])) {
            return call_user_func_array($callback, $args);
        }
        return null;
    }

    /**
     * Return URL that will trigger action on this call-back
     *
     * @return string
     */
    public function getURL()
    {
        return $this->app->url([$this->name=>'callback']);
    }

}