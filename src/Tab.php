<?php

namespace atk4\ui;

/**
 * One Tab of Tabs widget.
 */
class Tab extends Item
{
    /** @var string */
    public $path;

    /**
     * Sets path for tab.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $this->app->url($path) . '#';

        return $this;
    }

    /**
     * Rendering one tab view.
     */
    public function renderView()
    {
        if ($this->path) {
            $this->js(true)->tab(
                ['cache' => false, 'auto' => true, 'path' => $this->path, 'apiSettings' => ['data' => ['__atk_tab' => 1]]]
            );
        } else {
            $this->js(true)->tab();
        }

        if ($this->owner->activeTabName == $this->name) {
            $this->js(true)->click();
        }

        parent::renderView();
    }
}
