<?php

namespace atk4\ui;

/**
 * Paginate content using scroll event in JS.
 */
class jsPaginator extends jsCallback
{
    /**
     * The View that trigger scrolling event.
     *
     * @var null|View
     */
    public $view = null;

    /**
     * The js scroll plugin options
     *  - appendTo : the html selector where new content should be appendTo.
     *              Ex: For a table, the selector would be 'tbody'.
     *  - padding: Bottom padding need prior to perform a page request.
     *             Page request will be ask when container is scroll down and reach padding value.
     *  - initialPage: The initial page load.
     *                 The next page request will be initialPage + 1.
     *  - allowJsEval: Whether or not you want the plugin to evaluate javascript return by server response.
     *
     * @var null
     */
    public $options = [];

    public function init()
    {
        parent::init();

        if (!$this->view) {
            $this->view = $this->owner;
        }

        $this->view->js(true)->atkScroll(['uri'         => $this->getJSURL(),
                                          'uri_options' => $this->args,
                                          'options'     => $this->options,
                                         ]);
    }

    /**
     * Generate a js action that will set nextPage to atkScroll plugin.
     *
     * Method currently unused.
     *
     * @param int $page
     *
     * @return jQuery
     */
    public function jsNextPage($page)
    {
        return $this->view->js(true)->atkScroll('setNextPage', $page);
    }

    /**
     * Callback when container has been scroll to bottom.
     *
     * @param null|callable $fx
     */
    public function onScroll($fx = null)
    {
        if (is_callable($fx)) {
            if ($this->triggered()) {
                $page = @$_GET['page'];
                $this->set(function () use ($fx, $page) {
                    return call_user_func_array($fx, [$page]);
                });
            }
        }
    }
}
