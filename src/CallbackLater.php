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
     */
    public function set($fx = null, $fxArgs = null)
    {
        if ($this->getApp()->isRendering) {
            return parent::set($fx, $fxArgs);
        }

        $this->getApp()->onHook(App::HOOK_BEFORE_RENDER, function () use ($fx, $fxArgs) {
            return parent::set($fx, $fxArgs);
        });

        return null;
    }
}
