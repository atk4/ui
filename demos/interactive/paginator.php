<?php

date_default_timezone_set('UTC');
chdir('..');

require_once 'atk-init.php';




// Paginator which tracks its own position
\atk4\ui\Header::addTo($app, ['Paginator tracks its own position']);
\atk4\ui\Paginator::addTo($app, ['total' => 40, 'urlTrigger' => 'page']);

// Dynamically reloading paginator
\atk4\ui\Header::addTo($app, ['Dynamic reloading']);
$seg = \atk4\ui\View::addTo($app, ['ui' => 'blue segment']);
$label = \atk4\ui\Label::addTo($seg);
$bb = \atk4\ui\Paginator::addTo($seg, ['total' => 50, 'range' => 2, 'reload' => $seg]);
$label->addClass('blue ribbon');
$label->set('Current page: ' . $bb->page);

// Multiple dependent Paginators
\atk4\ui\Header::addTo($app, ['Local Sticky Usage']);
$seg = \atk4\ui\View::addTo($app, ['ui' => 'blue segment']);

$month = $seg->stickyGet('month') ?: 1;
$day = $seg->stickyGet('day') ?: 1;

// we intentionally left 31 days here and do not calculate number of days in particular month to keep example simple
$month_paginator = \atk4\ui\Paginator::addTo($seg, ['total' => 12, 'range' => 3, 'urlTrigger' => 'month']);
\atk4\ui\View::addTo($seg, ['ui'=>'hidden divider']);
$day_paginator = \atk4\ui\Paginator::addTo($seg, ['total' => 31, 'range' => 3, 'urlTrigger' => 'day']);
\atk4\ui\View::addTo($seg, ['ui'=>'hidden divider']);

$label = \atk4\ui\Label::addTo($seg);
$label->addClass('orange');
$label->set('Month: ' . $month . ' and Day: ' . $day);
