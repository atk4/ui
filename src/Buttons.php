<?php

namespace atk4\ui;

class Buttons extends View
{
    public $_class = 'buttons';

    public function renderView()
    {
        if ($this->content) {
            $this->addClass($this->content);
            $this->content = false;
        }
        parent::renderView();
    }
}
