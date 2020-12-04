<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * Works same as Callback but will be executed when the current
 * pass completes. Agile UI uses two-pass system - first to
 * initialize objects, second to render them. If you use this during
 * Init, then it will be executed.
 */
class CallbackLater extends Callback
{
    /**
     * Executes user-specified action before rendering or if App is
     * already in rendering state, then before output.
     *
     * @param \Closure $fx
     * @param array    $args
     *
     * @return mixed|null
     */
    public function set($fx = null, $args = null)
    {
        if (!$this->app) {
            throw new Exception('Call-back must be part of a RenderTree');
        }

        if ($this->app->is_rendering) {
            return parent::set($fx, $args);
        }

        $this->app->onHook(App::HOOK_BEFORE_RENDER, function () use ($fx, $args) {
            return parent::set($fx, $args);
        });
    }
}
