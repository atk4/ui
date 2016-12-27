<?php

namespace atk4\ui;

class Text extends View
{
    public $template = false;

    public function render()
    {
        return $this->content;
    }

    function getHTML() {
        return $this->content;
    }
}
