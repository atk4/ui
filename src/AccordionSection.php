<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * An accordion item in Accordion.
 */
class AccordionSection extends View
{
    public $defaultTemplate = 'accordion-section.html';

    /**
     * The accordion item title.
     *
     * @var string|null
     */
    public $title;

    /**
     * The accordion item virtual page.
     *
     * @var VirtualPage|null
     */
    public $virtualPage;

    public $icon = 'dropdown';

    /**
     * {@inheritdoc}
     */
    protected function renderView(): void
    {
        parent::renderView();

        $this->template->set('icon', $this->icon);

        if ($this->title) {
            $this->template->set('title', $this->title);
        }

        if ($this->virtualPage) {
            $this->template->set('item_id', $this->virtualPage->name);
            $this->template->set('path', $this->virtualPage->getJsUrl('cut'));
        }
    }
}
