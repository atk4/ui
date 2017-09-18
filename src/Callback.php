<?php

namespace atk4\ui;

use atk4\core\AppScopeTrait;
use atk4\core\DIContainerTrait;
use atk4\core\TrackableTrait;

/**
 * Add this object to your render tree and it will expose a unique URL which, when
 * executed directly will perform a PHP callback that you set().
 *
 * $button = $layout->add('Button');
 * $button->set('Click to do something')->link(
 *      $button
 *          ->add('Callback')
 *          ->set(function(){
 *              do_something();
 *          })
 *          ->getURL()
 *  );
 */
class Callback
{
    use TrackableTrait;
    use AppScopeTrait;
    use DIContainerTrait;

    /**
     * Will look for trigger in the POST data. Will re-use existing URL, but
     * $_POST[$this->name] will have to be set.
     */
    public $POST_trigger = false;

    /**
     * Contains either false if callback wasn't triggered or the value passed
     * as an argument to a call-back:.
     *
     * e.g. following URL of getURL('test') will result in $triggered = 'test';
     */
    public $triggered = false;

    /**
     * Initialize object and set default properties.
     *
     * @param array|string $defaults
     *
     * @throws Exception
     */
    public function __construct($defaults = [])
    {
        $this->setDefaults($defaults);
    }

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
        if (!$this->app) {
            throw new Exception(['Call-back must be part of a RenderTree']);
        }

        if ($this->POST_trigger) {
            if (isset($_POST[$this->name])) {
                $this->triggered = $_POST[$this->name];

                $t = $this->app->run_called;
                $this->app->run_called = true;
                $ret = call_user_func_array($callback, $args);
                $this->app->run_called = $t;

                return $ret;
            }
        } else {
            if (isset($_GET[$this->name])) {
                $this->triggered = $_GET[$this->name];

                $t = $this->app->run_called;
                $this->app->run_called = true;
                $this->app->stickyGet($this->name);
                $ret = call_user_func_array($callback, $args);
                $this->app->stickyForget($this->name);
                $this->app->run_called = $t;

                return $ret;
            }
        }
    }

    public function triggered()
    {
        return isset($_GET[$this->name]) ? $_GET[$this->name] : false;
    }

    /**
     * Return URL that will trigger action on this call-back.
     *
     * @return string
     */
    public function getURL($arg = 'callback')
    {
        if ($this->POST_trigger) {
            return $_SERVER['REQUEST_URI'];
        }

        return $this->app->url([$this->name=>$arg]);
    }
}
