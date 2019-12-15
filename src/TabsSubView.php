<?php

namespace atk4\ui;

/**
 * One Sub view of Tabs widget.
 */
class TabsSubView extends View
{
    public $class = ['ui tab'];

    public $dataTabName;

    public function setActive()
    {
        $this->owner->activeTabName = $this->dataTabName;
    }

    /**
     * {@inheritdoc}
     */
    public function renderView()
    {
        $this->setAttr('data-tab', $this->dataTabName);

        parent::renderView();
    }
}
