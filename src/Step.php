<?php

namespace atk4\ui;

/**
 * One step of the wizard.
 */
class Step extends View
{
    public $defaultTemplate;

    /**
     * Title to display in the step.
     *
     * @var string
     */
    public $title;

    /**
     * Description to show in the step under the title.
     *
     * @var string
     */
    public $description;

    /**
     * Link back to the wizard object.
     *
     * @var Wizard
     */
    public $wizard;

    /**
     * Icon appears to the left of the title in the step. You can disable icons for entire wizard.
     *
     * @var string
     */
    public $icon;

    /**
     * Will be automatically assigned 0, 1, 2, etc,.
     *
     * @var int
     */
    public $sequence;

    public function __construct($title)
    {
        $this->title = $title;
    }

    public function renderView()
    {
        $this->template->set('title', $this->title);
        $this->template->set('description', $this->description);

        if ($this->icon === false) {
            $this->template->del('has_icon');
        } else {
            $this->template->set('icon', $this->icon);
        }

        parent::renderView();
    }
}
