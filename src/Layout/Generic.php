<?php

declare(strict_types=1);

namespace atk4\ui\Layout;

use atk4\ui\Panel\Loadable;
use atk4\ui\View;

class Generic extends View
{
    /**
     * Add a loadable View.
     */
    public function addRightPanel(Loadable $panel): Loadable
    {
        return $this->owner->add($panel, 'RightPanel');
    }
}
