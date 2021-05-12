<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Paginator which tracks its own position
\Atk4\Ui\Header::addTo($app, ['Paginator tracks its own position']);
\Atk4\Ui\Paginator::addTo($app, ['total' => 40, 'urlTrigger' => 'page']);

// Dynamically reloading paginator
\Atk4\Ui\Header::addTo($app, ['Dynamic reloading']);
$seg = \Atk4\Ui\View::addTo($app, ['ui' => 'blue segment']);
$label = \Atk4\Ui\Label::addTo($seg);
$bb = \Atk4\Ui\Paginator::addTo($seg, ['total' => 50, 'range' => 2, 'reload' => $seg]);
$label->addClass('blue ribbon');
$label->set('Current page: ' . $bb->page);

// Multiple dependent Paginators
\Atk4\Ui\Header::addTo($app, ['Local Sticky Usage']);
$seg = \Atk4\Ui\View::addTo($app, ['ui' => 'blue segment']);

$month = $seg->stickyGet('month') ?: 1;
$day = $seg->stickyGet('day') ?: 1;

// we intentionally left 31 days here and do not calculate number of days in particular month to keep example simple
$monthPaginator = \Atk4\Ui\Paginator::addTo($seg, ['total' => 12, 'range' => 3, 'urlTrigger' => 'month']);
\Atk4\Ui\View::addTo($seg, ['ui' => 'hidden divider']);
$dayPaginator = \Atk4\Ui\Paginator::addTo($seg, ['total' => 31, 'range' => 3, 'urlTrigger' => 'day']);
\Atk4\Ui\View::addTo($seg, ['ui' => 'hidden divider']);

$label = \Atk4\Ui\Label::addTo($seg);
$label->addClass('orange');
$label->set('Month: ' . $month . ' and Day: ' . $day);
