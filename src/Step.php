<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * One step of the wizard.
 */
class Step extends View
{
    public $defaultTemplate;

    /** @var string Title to display in the step. */
    public $title;

    /** @var string Description to show in the step under the title. */
    public $description;

    /** @var Wizard Link back to the wizard object. */
    public $wizard;

    /** @var string|false Icon appears to the left of the title in the step. You can disable icons for entire wizard. */
    public $icon;

    /** @var int Will be automatically assigned 0, 1, 2, etc,. */
    public $sequence;

    public function __construct($title)
    {
        $this->title = $title;
    }

    protected function renderView(): void
    {
        $this->template->set('title', $this->title);
        $this->template->set('description', $this->description);

        if ($this->icon === false) {
            $this->template->del('hasIcon');
        } else {
            $this->template->set('icon', $this->icon);
        }

        parent::renderView();
    }
}
