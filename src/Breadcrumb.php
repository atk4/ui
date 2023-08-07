<?php

declare(strict_types=1);

namespace Atk4\Ui;

class Breadcrumb extends Lister
{
    public $ui = 'breadcrumb';

    public $defaultTemplate = 'breadcrumb.html';

    /** @var array */
    public $path = [];

    /** @var string */
    public $dividerClass = 'right angle icon';

    /**
     * Adds a new link that will appear on the right.
     *
     * @param string                                   $section Title of link
     * @param string|array<0|string, string|int|false> $link    Link itself
     *
     * @return $this
     */
    public function addCrumb($section = null, $link = null)
    {
        if (is_array($link)) {
            $link = $this->url($link);
        }
        $this->path[] = ['section' => $section, 'link' => $link, 'divider' => $this->dividerClass];

        return $this;
    }

    /**
     * Converts the last crumb you added into a title. This may be convenient if you add
     * crumbs conditionally and the last should remain as a title.
     *
     * @return $this
     */
    public function popTitle()
    {
        $title = array_pop($this->path);
        $this->set($title['section'] ?? '');

        return $this;
    }

    /**
     * Adds a new link that will appear on the left.
     *
     * @param string       $section Title of link
     * @param string|array $link    Link itself
     *
     * @return $this
     */
    public function addCrumbReverse($section = null, $link = null)
    {
        array_unshift($this->path, ['section' => $section, 'link' => $link]);

        return $this;
    }

    protected function renderView(): void
    {
        $this->setSource($this->path);

        parent::renderView();
    }
}
