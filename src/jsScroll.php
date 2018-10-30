<?php

namespace atk4\ui;

class jsScroll extends jsCallback
{
    /**
     * The View that need scrolling.
     *
     * @var null| \atk4\ui\View
     */
    public $view = null;

    public function init()
    {
        parent::init();
        if (!$this->view) {
            $this->view = $this->owner;
        }
        $this->view->js(true)->atkScroll(['uri'       => $this->getJSURL(),
                                              'uri_options' => $this->args,
                                             ]);
    }

    public function jsNextPage($page)
    {
        $this->view->js(true)->atkScroll('nextPage', $page);
        return $this;
    }

    /**
     * Callback when container has been reorder.
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