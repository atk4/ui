<?php
/**
 * Flyable content interface.
 */

namespace atk4\ui\Panel;

use atk4\ui\jsCallback;

interface LoadableContent
{
    public function setCb(jsCallback $cb);

    public function getCallbackUrl() :string;

    /**
     * The callback for loading content.
     *
     * @param callable $callable
     */
    public function onLoad(callable $callable);
}