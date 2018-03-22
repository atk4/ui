<?php

namespace atk4\ui;

/**
 * This class generates action, that will be able to loop-back to the callback method.
 */
class jsReload implements jsExpressionable
{
    /**
     * Specifies which view to reload. Use constructor to set.
     *
     * @var View
     */
    public $view = null;

    /**
     * A Js function to execute after reload is complete and onSuccess is execute.
     *
     * @var jsExpression
     */
    public $afterSuccess = null;

    /**
     * If defined, they will be added at the end of your URL.
     * Value in ARG can be either string or jsExpressionable.
     *
     * @var array
     */
    public $args = [];

    public function __construct($view, $args = [], $afterSuccess = null)
    {
        $this->view = $view;
        $this->args = $args;
        $this->afterSuccess = $afterSuccess;
    }

    public function jsRender()
    {
        $final = (new jQuery($this->view))
            ->atkReloadView(
                [
                    'uri'          => $this->view->jsURL(['__atk_reload'=>$this->view->name]),
                    'uri_options'  => $this->args,
                    'afterSuccess' => $this->afterSuccess ? $this->afterSuccess->jsRender() : null,
                ]
            );

        return $final->jsRender();
    }
}
