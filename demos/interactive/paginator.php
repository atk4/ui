<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Header;
use Atk4\Ui\Label;
use Atk4\Ui\Paginator;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Paginator which tracks its own position
Header::addTo($app, ['Paginator tracks its own position']);
Paginator::addTo($app, ['total' => 40, 'urlTrigger' => 'page']);

// dynamically reloading paginator
Header::addTo($app, ['Dynamic reloading']);
$seg = View::addTo($app, ['ui' => 'blue segment']);
$label = Label::addTo($seg);
$bb = Paginator::addTo($seg, ['total' => 50, 'range' => 2, 'reload' => $seg]);
$label->addClass('blue ribbon');
$label->set('Current page: ' . $bb->page);

// multiple dependent Paginators
Header::addTo($app, ['Local Sticky Usage']);
$seg = View::addTo($app, ['ui' => 'blue segment']);

$month = $seg->stickyGet('month') ?? 1;
$day = $seg->stickyGet('day') ?? 1;

// we intentionally left 31 days here and do not calculate number of days in particular month to keep example simple
$monthPaginator = Paginator::addTo($seg, ['total' => 12, 'range' => 3, 'urlTrigger' => 'month']);
View::addTo($seg, ['ui' => 'hidden divider']);
$dayPaginator = Paginator::addTo($seg, ['total' => 31, 'range' => 3, 'urlTrigger' => 'day']);
View::addTo($seg, ['ui' => 'hidden divider']);

$label = Label::addTo($seg);
$label->addClass('orange');
$label->set('Month: ' . $month . ' and Day: ' . $day);
