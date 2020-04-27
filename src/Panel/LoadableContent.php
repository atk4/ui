<?php
/**
 * LoadableContent interface.
 */

namespace atk4\ui\Panel;

use atk4\ui\jsCallback;

interface LoadableContent
{
    /**
     * Add jsCallback.
     *
     * @return mixed
     */
    public function setCb(jsCallback $cb);

    /**
     * Return js Callback url string.
     */
    public function getCallbackUrl(): string;

    /**
     * The callback for loading content.
     */
    public function onLoad(callable $callable);
}
