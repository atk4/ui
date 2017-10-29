<?php

namespace atk4\ui;

/**
 * Class implements Loader, which is a View that will dynamically render it's content.
 * To provide content for a loader, use set() callback.
 */
class Loader extends View
{
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
     * Specify which event will cause Loader to begen fetching it's actual data. In some cases
     * you would want to wait. You can set a custom JavaScript event name then trigger() it.
     *
     * Default value is `true` which means loading will take place as soon as possible. Setting this
     * to `false` will disable event entirely.
     *
     * @var bool|string
     */
    public $loadEvent = true;

    public $ui = 'ui segment';

    /** @var Callback for triggering */
    protected $cb;

    public function init()
    {
        parent::init();

        if (!$this->shim) {
            $this->shim = ['View', 'ui' => 'active centered inline loader'];
        }

        $this->cb = $this->add('Callback');
    }

    /**
     * Set callback function for this loader.
     *
     * The loader view is pass as an argument to the loader callback function.
     * This allow to easily update the loader view content within the callback.
     *  $l1 = $layout->add('Loader');
     *  $l1->set(function ($loader_view) {
     *    do_long_processing_action();
     *    $loader_view->set('new content');
     *  });
     *
     * Or
     *  $l1->set([$my_object, 'run_long_process']);
     *
     * NOTE: default values are like that due ot PHP 7.0 warning:
     * Declaration of atk4\ui\Loader::set($fx, $args = Array) should be compatible with atk4\ui\View::set($arg1 = Array, $arg2 = NULL)
     *
     * @param callable $fx
     * @param array    $args
     *
     * @throws Exception
     *
     * @return $this
     */
    public function set($fx = [], $args = null)
    {
        if (!is_callable($fx)) {
            throw new Exception('Error: Need to pass a callable function to Loader::set()');
        }

        $this->cb->set(function () use ($fx) {
            call_user_func($fx, $this);
            $this->app->terminate($this->renderJSON());
        });

        return $this;
    }

    /**
     * Automatically call the jsLoad on a supplied event unless it was already triggered
     * or if user have invoked jsLoad manually.
     */
    public function renderView()
    {
        if (!$this->cb->triggered()) {
            if ($this->loadEvent) {
                $this->js($this->loadEvent, $this->jsLoad());
            }
            $this->add($this->shim);
        }

        return parent::renderView();
    }

    /**
     * Return a js action that will trigger the loader to start.
     *
     * @param array $args
     *
     * @return mixed
     */
    public function jsLoad($args = [])
    {
        return $this->js()->atkReloadView([
            'uri'         => $this->cb->getURL(),
            'uri_options' => $args,
        ]);
    }
}
