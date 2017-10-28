<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * One Tab of Tabs widget.
 */
class Tab extends Item
{
    public $path = null;

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function renderView()
    {
        if ($this->path) {
            $this->js(true)->tab(
                ['cache' => false, 'auto' => true, 'path' => $this->path]
            );
        } else {
            $this->js(true)->tab();
        }

        parent::renderView();
    }
}
