<?php

namespace atk4\ui\Layout;

use atk4\ui\Panel\Loadable;

class Generic extends \atk4\ui\View
{
    /**
     * Add a loadable View.
     *
     * @param Loadable $panel
     *
     * @return Loadable
     * @throws \atk4\core\Exception
     */
    public function addRightPanel(Loadable $panel) :Loadable
    {
        return $this->owner->add($panel, 'RightPanel');
    }
}
