<?php

require 'init.php';
require 'database.php';

$app->add(['Button', 'Dynamic scroll in Container', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['scroll-container']);
$app->add(['Button', 'Dynamic scroll in Grid using Container', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['scroll-grid-container']);
$app->add(['View', 'ui' => 'ui clearing divider']);



$app->add(['Header', 'Dynamic scroll in Grid']);

$g = $app->add(['Grid', 'menu' => false]);
$m = $g->setModel(new Country($db));

$g->addJsPaginator(30);
