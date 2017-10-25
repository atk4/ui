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
    public $loader = null;
    public $needOnPageLoad = false;
    public $loaderCallback = null;
    public $ui = '';

    public $args = null;

    public function init()
    {
        parent::init();

        //supply default loader view if none is supply.
        if (!$this->loader) {
           $this->loader = new View(['ui' => 'segment padded']);
        }

        $this->add($this->loader);
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
     * @param callable $fx
     * @param array $args
     *
     * @return $this
     * @throws Exception
     */
    public function set($fx, $args = [])
    {
        if (!is_object($fx) && !($fx instanceof Closure)) {
            throw new Exception('Error: Need to pass a closure function to Loader::set()');
        }

        $this->loaderCallback = $this->loader->add('CallbackLater');

        if ($this->loaderCallback->triggered() && $fx) {
            call_user_func($fx, $this->loader);
            $this->app->terminate($this->renderJSON());
        } else {
            if ($this->needOnPageLoad) {
                $this->jsStartLoader(true, $args);
            }
        }

        return $this;
    }

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
     * @param array $args
     *
     * @return mixed
     */
    public function jsStartLoader($when = null, $args = [])
    {
        return $this->loader->js($when)->atkReloadView([
            'uri'         => $this->loaderCallback->getURL(),
            'uri_options' => $args,
        ]);
    }
}
