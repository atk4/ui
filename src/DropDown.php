<?php

namespace atk4\ui;

class DropDown extends Lister
{
    // @inheritdoc
    public $ui = 'dropdown';

    // @inheritdoc
    public $defaultTemplate = 'dropdown.html';

    public $js;

    /**
     * {@inheritdoc}
     */
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
