<?php

declare(strict_types=1);

namespace atk4\ui;

class Layout extends View
{
    /**
     * Add a loadable View.
     */
    public function addRightPanel(Panel\Loadable $panel): Panel\Loadable
    {
        return $this->owner->add($panel, 'RightPanel');
    }
}
