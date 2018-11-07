<?php

namespace atk4\ui;

/**
 * An accordion item in Accordion.
 *
 * @package atk4\ui
 */
class AccordionItem extends View
{
    public $defaultTemplate = 'accordion-item.html';

    /**
     * The accordion item title.
     *
     * @var string|null
     */
    public $title = null;

    /**
     * The accordion item virtual page.
     *
     * @var null|VirtualPage
     */
    public $virtualPage = null;

    /**
     *
     * {@inheritdoc}
     */
    public function renderView()
    {
        parent::renderView();

        if ($this->title) {
            $this->template->set('title', $this->title);
        }

        if ($this->virtualPage) {
            $this->template->set('item_id', $this->virtualPage->name);
            $this->template->set('path', $this->virtualPage->getJSURL('cut'));
        }
    }
}
