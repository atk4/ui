<?php

declare(strict_types=1);

namespace Atk4\Ui;

class Layout extends View
{
    /**
     * Add a loadable View.
     */
    public function addRightPanel(Panel\Loadable $panel): Panel\Loadable
    {
        return $this->getOwner()->add($panel, 'RightPanel');
    }
}
