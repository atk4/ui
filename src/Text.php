<?php

namespace atk4\ui;

class Text extends View
{
    public $defaultTemplate = false;

    public function render()
    {
        return $this->content;
    }

    public function getHTML()
    {
        return $this->content;
    }
}
