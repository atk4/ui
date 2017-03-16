<?php

namespace atk4\ui;

use atk4\core\AppScopeTrait;
use atk4\core\TrackableTrait;

/**
 * Works same as Callback but will be executed when the current
 * pass completes. Agile UI uses two-pass system - first to
 * initialize objects, second to render them. If you use this during
 * Init, then it will be executed 
 */
class CallbackLater extends Callback
{
    public function set($callback, $args = []) {
        if (!$this->app) {
            throw new Exception(['Call-back must be part of a RenderTree']);
        }

        if ($this->app->is_rendering) {
            $hook = 'beforeOutput';
        } else {
            $hook = 'beforeRender';
        }

        $this->app->addHook($hook, function(...$args) use($callback) {
            array_shift($args); // Hook will have first argument pointing to the app. We don't need that.
            return parent::set($callback, $args);
        }, $args);
    }
}
