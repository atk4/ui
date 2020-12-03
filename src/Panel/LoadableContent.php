<?php

declare(strict_types=1);
/**
 * LoadableContent interface.
 */

namespace Atk4\Ui\Panel;

use Atk4\Ui\Callback;

interface LoadableContent
{
    /**
     * Add JsCallback.
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
    public function onLoad(\Closure $fx);
}
