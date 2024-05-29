<?php

declare(strict_types=1);

namespace Atk4\Ui\Panel;

use Atk4\Ui\Callback;
use Atk4\Ui\View;

/**
 * Slide Panel Content.
 *
 * @method Right getOwner()
 */
class Content extends View implements LoadableContent
{
    public $defaultTemplate = 'panel/content.html';

    /** @var Callback */
    public $cb;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->addClass('atk-panel-content');
        $this->setCb(new Callback());
    }

    #[\Override]
    public function getCallbackUrl(): string
    {
        return $this->cb->getJsUrl();
    }

    #[\Override]
    public function setCb(Callback $cb): void
    {
        $this->cb = $this->add($cb); // @phpstan-ignore assign.propertyType
    }

    /**
     * Will load content into callback.
     *
     * @param \Closure($this): void $fx
     */
    #[\Override]
    public function onLoad(\Closure $fx): void
    {
        $this->cb->set(function () use ($fx) {
            $fx($this);

            $this->cb->terminateJsonIfCanTerminate($this);
        });
    }

    /**
     * Return an array of CSS selector where content will be
     * cleared on reload.
     */
    public function getClearSelector(): array
    {
        return ['.atk-panel-content'];
    }
}
