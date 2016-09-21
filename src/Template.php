<?php

namespace atk4\ui;

class Template {

    public $template = null;

    function __construct($file) {
        $this->template = file_get_contents($file);
    }

    function render() {
        return $this->template;
    }
}
