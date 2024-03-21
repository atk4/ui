<?php

declare(strict_types=1);

namespace Atk4\Ui\UserAction;

use Atk4\Ui\Loader;
use Atk4\Ui\View;

/**
 * @internal
 */
trait InnerLoaderTrait
{
    protected Loader $loader;

    #[\Override]
    protected function renderView(): void
    {
        if ($this->loader->cb->isTriggered()) {
            parent::renderView();
        } else {
            $this->template = $this->getApp()->loadTemplate('element.html');
            View::renderView();
        }
    }

    #[\Override]
    protected function recursiveRender(): void
    {
        if ($this->loader->cb->isTriggered()) {
            parent::recursiveRender();
        }
    }
}
