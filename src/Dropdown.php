<?php

namespace atk4\ui;

class Dropdown extends Lister
{
    public $ui = 'dropdown';

    public $js;

    public $defaultTemplate = 'dropdown.html';

    public function renderView()
    {
        if (isset($this->js)) {
            $this->js(true)->dropdown($this->js);
        } else {
            $this->js(true)->dropdown();
        }

        return parent::renderView();
    }
}
