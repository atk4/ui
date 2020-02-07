<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Implements a more sophisticated and interractive Data-Table component.
 */
class BreadCrumb extends Lister
{
    /** @var array */
    public $path = [];

    /** @var string */
    public $defaultTemplate = 'breadcrumb.html';

    /** @var string */
    public $dividerClass = 'right angle icon';

    /** @var string */
    public $ui = 'breadcrumb';

    /**
     * Adds a new link that will appear on the right.
     *
     * @param string       $section Title of link
     * @param string|array $link    Link itself
     */
    public function addCrumb($section = null, $link = null)
    {
        if (is_array($link)) {
            $link = $this->url($link);
        }
        $this->path[] = ['section' => $section, 'link' => $link, 'divider' => $this->dividerClass];
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
        $this->set($title['section'] ?: '');

        return $this;
    }

    /**
     * Adds a new link that will appear on the left.
     *
     * @param string       $section Title of link
     * @param string|array $link    Link itself
     */
    public function addCrumbReverse($section = null, $link = null)
    {
        array_unshift($this->path, ['section' => $section, 'link' => $link]);
    }

    /**
     * Renders view.
     */
    public function renderView()
    {
        $this->setSource($this->path);

        parent::renderView();
    }
}
