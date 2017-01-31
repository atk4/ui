<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Place menu 
 */
class Item extends View
{
    public $label;

    function renderView()
    {
        if ($this->label) {
            $this->add(new Label($this->label));
        }

        parent::renderView();
    }

}
