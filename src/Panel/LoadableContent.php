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
     * @param jsCallback $cb
     *
     * @return mixed
     */
    public function setCb(jsCallback $cb);

    /**
     * Return js Callback url string.
     * @return string
     */
    public function getCallbackUrl() :string;

    /**
     * The callback for loading content.
     *
     * @param callable $callable
     */
    public function onLoad(callable $callable);
}