<?php

declare(strict_types=1);
/**
 * Slide Panel Content.
 */

namespace atk4\ui\Panel;

use atk4\ui\Callback;
use atk4\ui\View;

class Content extends View implements LoadableContent
{
    public $defaultTemplate = 'panel/content.html';
    public $cb;

    public function init(): void
    {
        parent::init();
        $this->addClass('atk-panel-content');
        $this->setCb(new Callback());
    }

    /**
     * Return callback url for panel options.
     */
    public function getCallbackUrl(): string
    {
        return $this->cb->getJsUrl();
    }

    /**
     * Set callback for panel.
     *
     * @return mixed|void
     */
    public function setCb(Callback $cb)
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
            call_user_func($callback, $this);
            $this->cb->terminateJson();
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
