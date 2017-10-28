<?php

namespace atk4\ui;

/**
 * Class implements Loader.
 *
 * You may supply your own view as the loader view.
 *  - if not, view is supply by default.
 *  ex: $loader = new Loader(['loader' => new View()]);
 */
class Loader extends View
{
    /**
     * Set to a custom object or inject properties into default loader.
     */
    public $loader;

    public $loadEvent = true;

    public $ui = 'ui segment';

    public $args = null;

    public function init()
    {
        parent::init();

        $this->loader = $this->factory('LoaderShim', $this->loader);
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
        if (!is_object($fx) && !($fx instanceof Closure)) {
            throw new Exception('Error: Need to pass a closure function to Loader::set()');
        }

        $this->loaderCallback = $this->add('Callback');

        if ($this->loaderCallback->set(function () use ($fx) {
            call_user_func($fx, $this);
            $this->app->terminate($this->renderJSON());
        }));

        return $this;
    }

    /**
     * Automatically load if jsLoad() wasn't called already.
     */
    public function renderView()
    {
        if (!$this->loaderCallback->triggered() && !$this->_jsLoad_invoked && $this->loadEvent) {
            $this->js($this->loadEvent, $this->jsLoad());
            $this->add($this->loader);
        }

        return parent::renderView();
    }

    protected $_jsLoad_invoked = false;

    /**
     * Return loader callback url when set.
     *
     * @return string|null
     */
    public function getLoaderUrl()
    {
        return ($this->loaderCallback) ? $this->loaderCallback->getUrl() : null;
    }

    /**
     * Return a js action that will triggered the loader to start.
     *
     * @param bool|null $when
     * @param array     $args
     *
     * @return mixed
     */
    public function jsLoad($args = [])
    {
        return $this->js()->atkReloadView([
            'uri'         => $this->loaderCallback->getURL(),
            'uri_options' => $args,
        ]);
    }
}
