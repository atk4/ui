<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * @phpstan-require-extends View
 *
 * @internal
 */
trait LoaderInnerTrait
{
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
