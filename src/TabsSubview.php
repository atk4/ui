<?php

declare(strict_types=1);

namespace Atk4\Ui;

/**
 * @method Tabs getOwner()
 */
class TabsSubview extends View
{
    public array $class = ['ui tab'];

    /** @var string */
    public $dataTabName;

    public function setActive(): void
    {
        $this->getOwner()->activeTabName = $this->dataTabName;
    }

    protected function renderView(): void
    {
        $this->setAttr('data-tab', $this->dataTabName);

        parent::renderView();
    }
}
