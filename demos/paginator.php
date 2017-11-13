<?php

date_default_timezone_set('UTC');
include 'init.php';

$app->add(['Header', 'Paginator tracks its own position']);
$app->add(['Paginator', 'total' => 40]);

$app->add(['Header', 'Dynamic reloading']);
$seg = $app->add(['View', 'ui' => 'blue segment']);
$label = $seg->add(['Label']);
$bb = $seg->add(['Paginator', 'total' => 50, 'range' => 2, 'reload' => $seg]);
$label->addClass('blue ribbon');
$label->set('Current page: '.$bb->page);
