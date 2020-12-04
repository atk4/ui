<?php

declare(strict_types=1);

namespace Atk4\Ui;

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

    protected function renderView(): void
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
