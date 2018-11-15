<?php


require 'init.php';
require 'database.php';

$app->add(['Button', 'Dynamic scroll in Grid using Container', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['scroll-grid-container']);

$app->add(['View', 'ui' => 'ui clearing divider']);

$g = $app->add(['Grid', 'menu' => false]);
$m = $g->setModel(new Country($db));

$g->addJsPaginator(30);
