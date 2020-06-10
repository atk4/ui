<?php
/**
 * LoadableContent interface.
 */

namespace atk4\ui\Panel;

use atk4\ui\Callback;

interface LoadableContent
{
    /**
     * Add jsCallback.
     *
     * @return mixed
     */
    public function setCb(Callback $cb);

    /**
     * Return js Callback url string.
     */
    public function getCallbackUrl(): string;

    /**
     * The callback for loading content.
     */
    public function onLoad(callable $callable);
}
