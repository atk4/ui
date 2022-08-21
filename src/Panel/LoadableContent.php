<?php

declare(strict_types=1);

namespace Atk4\Ui\Panel;

use Atk4\Ui\Callback;

interface LoadableContent
{
    /**
     * Add JsCallback.
     */
    public function setCb(Callback $cb): void;

    /**
     * Return js Callback url string.
     */
    public function getCallbackUrl(): string;

    /**
     * The callback for loading content.
     */
    public function onLoad(\Closure $fx): void;
}
