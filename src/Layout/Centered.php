<?php

namespace atk4\ui\Layout;

/**
 * Implements a fixed-width single-column bevel in the middle of the page, centered
 * horizontally and vertically. Icon / Title will apear above the bevel.
 *
 * Bevel will use some padding and will contain your Content.
 * This layout is handy for a simple and single-purpose applications.
 */
class Centered extends Generic
{
    use \atk4\core\DebugTrait;

    public $defaultTemplate = 'layout/centered.html';

    public function init()
    {
        parent::init();

        // set application's title

        $this->template->trySet('title', $this->app->title);
    }
}
