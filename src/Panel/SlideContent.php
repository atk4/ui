<?php
/**
 * Slide Panel Content.
 */

namespace atk4\ui\Panel;

use atk4\ui\jQuery;
use atk4\ui\jsCallback;
use atk4\ui\jsExpression;
use atk4\ui\View;

class SlideContent extends View implements SlidableContent
{
    public $defaultTemplate = 'panel/slide-content.html';
    public $cb = null;

    public function init()
    {
        parent::init();
        $this->addClass('atk-slide-content');
        $this->setCb(new jsCallback(['appSticky' => true]));
    }

    public function getCallbackUrl() :string
    {
        return $this->cb->getJSURL();
    }

    public function setCb(jsCallback $cb)
    {
        $this->cb = $this->add($cb);
    }

    /**
     * Will load content into callback.
     * Callable will receive this view as first parameter.
     *
     * @param $callback
     */
    public function onLoad($callback)
    {
        $this->cb->set(function() use($callback) {
            if ($this->cb->triggered()) {
                call_user_func($callback, $this);
                $this->app->terminateJSON($this);
            }
        });
    }

    /**
     * Return an array of css selector where content will be
     * cleared on reload.
     *
     * @return array
     */
    public function getClearSelector() :array
    {
        return ['.atk-slide-content'];
    }

}
