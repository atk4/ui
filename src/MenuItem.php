<?php

declare(strict_types=1);

namespace Atk4\Ui;

class MenuItem extends View
{
    /** @var string Specify a label for this menu item. */
    public $label;

    /** @var string Specify icon for this menu item. */
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
