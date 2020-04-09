<?php

namespace atk4\ui\Layout;

use atk4\ui\Panel\Slidable;

class Generic extends \atk4\ui\View
{

    public function addRightPanel(Slidable $panel) :Slidable
    {
        return $this->owner->add($panel, 'RightPanel');
    }
}
