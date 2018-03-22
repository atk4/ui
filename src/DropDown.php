<?php

namespace atk4\ui;

class DropDown extends Lister
{
    public $ui = 'dropdown';

    public $defaultTemplate = 'dropdown.html';

    /**
     * Supply an optional parameter to the drop-down.
     *
     * @var array will be converted to json passed into dropdown()
     */
    public $js;

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
