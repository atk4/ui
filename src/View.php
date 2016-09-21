<?php

namespace atk4\ui;

/**
 * Implements a generic view. Can be used and will produce a single DIV.
 */
class View {

    public $template = null;

    /**
     * When you call render() this will be populated.
     */
    public $output;

    /**
     * When you call render() this will be populated with JavaScript
     * chains.
     */
    public $js;

    public $model;

    function __construct($template = null) {
        $this->template = $template;
    }

    function render() {
        $this->output = $this->template->render();
    }

    function setModel($m) {
        $this->model = $m;
    }

    function getHTML()
    {
        return $this->output;
    }

    function getJS()
    {
        return '';
    }

}
