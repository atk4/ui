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
     * @param string|array<0|string, string|int|false> $page
     *
     * @return $this
     */
    public function setPath($page)
    {
        $this->path = $this->getApp()->url($page) . '#';

        return $this;
    }

    #[\Override]
    protected function renderView(): void
    {
        $this->settings = array_merge($this->settings, ['autoTabActivation' => false]);

        if ($this->path) {
            $this->settings['cache'] = false;
            $this->settings['auto'] = true;
            $this->settings['path'] = $this->path;
            $this->settings['apiSettings']['data']['__atk_tab'] = 1;
        }

        $this->js(true)->tab($this->settings);

        if ($this->getOwner()->activeTabName === $this->name) {
            $this->js(true)->click();
        }

        parent::renderView();
    }
}
