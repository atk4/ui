<?php

namespace atk4\ui;

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
    public function renderView()
    {
        parent::renderView();

        $this->template->set('icon', $this->icon);

        if ($this->title) {
            $this->template->set('title', $this->title);
        }

        if ($this->virtualPage) {
            $this->template->set('item_id', $this->virtualPage->name);
            $this->template->set('path', $this->virtualPage->getJSURL('cut'));
        }
    }
}
