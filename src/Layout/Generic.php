<?php

namespace atk4\ui\Layout;

use atk4\ui\Panel\Loadable;
use atk4\ui\Panel\Slidable;

class Generic extends \atk4\ui\View
{

    public function addRightPanel(Loadable $panel) :Loadable
    {
        return $this->owner->add($panel, 'RightPanel');
    }
}
