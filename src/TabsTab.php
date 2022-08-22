<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * @method Tabs getOwner()
 */
class TabsTab extends MenuItem
{
    /** @var string */
    public $path;

    /** @var array Tab settings */
    public $settings = [];

    /**
     * Sets path for tab.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $this->getApp()->url($path) . '#';

        return $this;
    }

    /**
     * Rendering one tab view.
     */
    protected function renderView(): void
    {
        $this->settings = array_merge($this->settings, ['autoTabActivation' => false]);

        if ($this->path) {
            $this->settings = array_merge_recursive($this->settings, [
                'cache' => false,
                'auto' => true,
                'path' => $this->path,
                'apiSettings' => ['data' => ['__atk_tab' => 1]],
            ]);
        }

        $this->js(true)->tab($this->settings);

        if ($this->getOwner()->activeTabName === $this->name) {
            $this->js(true)->click();
        }

        parent::renderView();
    }
}
