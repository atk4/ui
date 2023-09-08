<?php

declare(strict_types=1);

namespace Atk4\Ui;

use Atk4\Ui\Js\JsChain;
use Atk4\Ui\Js\JsExpressionable;

/**
 * Dynamically render it's content.
 * To provide content for a loader, use set() callback.
 */
class Loader extends View
{
    public $ui = 'segment';

    /**
     * Shim is a filler object that is displayed inside loader while the actual content is fetched
     * from the server. You may supply an object here or a seed. This view will be replaced
     * by an actual content when loading stops. Additionally there will be loading indicator
     * on top of this content.
     *
     * @var View
     */
    public $shim;

    /**
     * Specify which event will cause Loader to begin fetching it's actual data. In some cases
     * you would want to wait. You can set a custom JavaScript event name then trigger() it.
     *
     * Default value is `true` which means loading will take place as soon as possible. Setting this
     * to `false` will disable event entirely.
     *
     * @var bool|string
     */
    public $loadEvent = true;

    /** @var Callback for triggering */
    public $cb;

    /** @var array URL arguments. */
    public $urlArgs = [];

    protected function init(): void
    {
        parent::init();

        if (!$this->shim) { // @phpstan-ignore-line
            $this->shim = [View::class, 'class' => ['padded segment'], 'style' => ['min-height' => '5em']];
        }

        if (!$this->cb) { // @phpstan-ignore-line
            $this->cb = Callback::addTo($this);
        }
    }

    /**
     * Set callback function for this loader.
     *
     * The loader view is pass as an argument to the loader callback function.
     * This allow to easily update the loader view content within the callback.
     *  $l1 = Loader::addTo($layout);
     *  $l1->set(function (Loader $p) {
     *    do_long_processing_action();
     *    $p->set('new content');
     *  });
     *
     * @param \Closure($this): void $fx
     *
     * @return $this
     */
    public function set($fx = null)
    {
        if (!$fx instanceof \Closure) {
            throw new \TypeError('$fx must be of type Closure');
        } elseif ('func_num_args'() > 1) {
            throw new Exception('Only one argument is needed by Loader::set()');
        }

        $this->cb->set(function () use ($fx) {
            $fx($this);
            $this->cb->terminateJson($this);
        });

        return $this;
    }

    /**
     * Automatically call the jsLoad on a supplied event unless it was already triggered
     * or if user have invoked jsLoad manually.
     */
    protected function renderView(): void
    {
        if (!$this->cb->isTriggered()) {
            if ($this->loadEvent) {
                $this->js($this->loadEvent, $this->jsLoad($this->urlArgs));
            }
            $this->add($this->shim);
        }

        parent::renderView();
    }

    /**
     * Return a JS action that will trigger the loader to start.
     *
     * @param string $storeName
     *
     * @return JsChain
     */
    public function jsLoad(array $args = [], array $apiConfig = [], $storeName = null): JsExpressionable
    {
        return $this->js()->atkReloadView([
            'url' => $this->cb->getUrl(),
            'urlOptions' => $args,
            'apiConfig' => $apiConfig !== [] ? $apiConfig : null,
            'storeName' => $storeName,
        ]);
    }
}
