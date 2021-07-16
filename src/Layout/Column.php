<?php

declare(strict_types=1);

namespace Atk4\Ui\Layout;

use Atk4\Ui\Layout;

/**
 * Implements a single content column application, typically used in your favourite
 * social application.
 *
 * Can contain up to 3 vertical columns left/right containing any
 * arbitrary data and the middle one typically containing infinite scrollable media feed.
 *
 * Sticky top-bar for simple navigation and three flexible areas for flexible use.
 */
class Column extends Layout
{
    public $defaultTemplate = 'layout/column.html';

    public $menu;

    public $leftColumn;
    public $rightColumn;
}
