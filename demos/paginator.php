<?php

date_default_timezone_set('UTC');
include_once __DIR__ . '/init.php';

// Paginator which tracks its own position
$app->add(['Header', 'Paginator tracks its own position']);
$app->add(['Paginator', 'total' => 40, 'urlTrigger' => 'page']);

// Dynamically reloading paginator
$app->add(['Header', 'Dynamic reloading']);
$seg = $app->add(['View', 'ui' => 'blue segment']);
$label = $seg->add(['Label']);
$bb = $seg->add(['Paginator', 'total' => 50, 'range' => 2, 'reload' => $seg]);
$label->addClass('blue ribbon');
$label->set('Current page: '.$bb->page);

// Multiple dependent Paginators
$app->add(['Header', 'Local Sticky Usage']);
$seg = $app->add(['View', 'ui' => 'blue segment']);

$month = $seg->stickyGet('month') ?: 1;
$day = $seg->stickyGet('day') ?: 1;

// we intentionally left 31 days here and do not calculate number of days in particular month to keep example simple
$month_paginator = $seg->add(['Paginator', 'total' => 12, 'range' => 3, 'urlTrigger' => 'month']);
$seg->add(['ui'=>'hidden divider']);
$day_paginator = $seg->add(['Paginator', 'total' => 31, 'range' => 3, 'urlTrigger' => 'day']);
$seg->add(['ui'=>'hidden divider']);

$label = $seg->add(['Label']);
$label->addClass('orange');
$label->set('Month: '.$month.' and Day: '.$day);
