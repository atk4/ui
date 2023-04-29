<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * An accordion item in Accordion.
 *
 * @method Accordion getOwner()
 */
class AccordionSection extends View
{
    public $defaultTemplate = 'accordion-section.html';

    /** @var string|null The accordion item title. */
    public $title;

    /** @var VirtualPage|null The accordion item virtual page. */
    public $virtualPage;

    /** @var string */
    public $icon = 'dropdown';

    protected function renderView(): void
    {
        parent::renderView();

        $this->template->set('icon', $this->icon);

        if ($this->title) {
            $this->template->set('title', $this->title);
        }

        if ($this->virtualPage) {
            $this->template->set('itemId', $this->virtualPage->name);
            $this->template->set('path', $this->virtualPage->getJsUrl('cut'));
        } else {
            // TODO hack to prevent rendering 'id=""'
            $this->template->set('itemId', $this->name . '-vp-unused');
        }
    }
}
