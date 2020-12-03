<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * One Sub view of Tabs widget.
 */
class TabsSubview extends View
{
    public $class = ['ui tab'];

    public $dataTabName;

    public function setActive()
    {
        $this->getOwner()->activeTabName = $this->dataTabName;
    }

    protected function renderView(): void
    {
        $this->setAttr('data-tab', $this->dataTabName);

        parent::renderView();
    }
}
