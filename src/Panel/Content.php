<?php
/**
 * Slide Panel Content.
 */

namespace atk4\ui\Panel;

use atk4\ui\jsCallback;
use atk4\ui\View;

class Content extends View implements LoadableContent
{
    public $defaultTemplate = 'panel/content.html';
    public $cb;

    public function init(): void
    {
        parent::init();
        $this->addClass('atk-panel-content');
        $this->setCb(new jsCallback(['appSticky' => true]));
    }

    /**
     * Return callback url for panel options.
     */
    public function getCallbackUrl(): string
    {
        return $this->cb->getJSURL();
    }

    /**
     * Set callback for panel.
     *
     * @throws \atk4\core\Exception
     *
     * @return mixed|void
     */
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
        $this->cb->set(function () use ($callback) {
            if ($this->cb->triggered()) {
                call_user_func($callback, $this);
                $this->app->terminateJSON($this);
            }
        });
    }

    /**
     * Return an array of css selector where content will be
     * cleared on reload.
     */
    public function getClearSelector(): array
    {
        return ['.atk-panel-content'];
    }
}
