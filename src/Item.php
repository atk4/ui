<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Place menu.
 */
class Item extends View
{
    /**
     * Specify a label for this menu item.
     *
     * @var string
     */
    public $label;

    /**
     * Specify icon for this menu item.
     *
     * @var string
     */
    public $icon;

    public function renderView()
    {
        if ($this->label) {
            Label::addTo($this, [$this->label]);
        }

        if ($this->icon) {
            Icon::addTo($this, [$this->icon]);
        }

        parent::renderView();
    }
}
