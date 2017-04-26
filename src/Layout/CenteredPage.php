<?php

namespace atk4\ui\Layout;

/**
 * Implements a fixed-width single-column bevel in the middle of the page,
 * centered horizontally.
 *
 * Bevel will use some padding and will contain your Content.
 * This layout is handy for horizontal centered pages.
 */
class CenteredPage extends Generic
{
    public $defaultTemplate = 'layout/centered_page.html';
}
