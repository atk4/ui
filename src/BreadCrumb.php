<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Implements a more sophisticated and interractive Data-Table component.
 */
class BreadCrumb extends Lister
{
    public $path = [];

    public $defaultTemplate = 'breadcrumb.html';

    public $dividerClass = 'right angle icon';

    public $ui = 'breadcrumb';

    /**
     * Adds a new page that will appear on the right
     *
     * @param string|array $item
     * @param string|array $action
     */
    public function addCrumb($section = null, $link = null) {
        if (is_array($link)) {
            $link = $this->url($link);
        }
        $this->path[] = ['section'=>$section, 'link'=>$link, 'divider'=>$this->dividerClass];
    }

    /**
     * Converts the last crumb you added into a title. This may be convenient if you add
     * crumbs conditionally and the last should remain as a title.
     */
    public function popTitle() {
        $title = array_pop($this->path);
        $this->set($title['section']);
        return $this;
    }

    public function addCrumbReverse($section = null, $link = null) {
        array_unshift($this->path, ['section'=>$section, 'link'=>$link]);
    }

    public function renderView()
    {
        $this->setSource($this->path);

        parent::renderView();
    }
}
