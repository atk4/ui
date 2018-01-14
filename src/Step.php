<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * One step of the wizard.
 */
class Step extends View
{
    /**
     * Use the parent's template.
     */
    public $defaultTemplate = null;

    public $title = null;

    public $description = null;

    public $wizard = null;

    public $icon = null;

    /**
     * Will be assigned 0, 1, 2, etc,.
     */
    public $sequence = null;

    public function __construct($title)
    {
        $this->title = $title;
    }

    public function renderView()
    {
        $this->template->set('title', $this->title);
        $this->template->set('description', $this->description);

        if ($this->icon == false) {
            $this->template->del('has_icon');
        } else {
            $this->template->set('icon', $this->icon);
        }

        parent::renderView();
    }
}
