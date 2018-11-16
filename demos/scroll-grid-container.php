<?php

require 'init.php';
require 'database.php';

$app->add(['Button', 'Dynamic scroll in Grid', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['scroll-grid']);
$app->add(['View', 'ui' => 'ui clearing divider']);



$app->add(['Header', 'Dynamic scroll in Grid with fixed column headers']);

$g = $app->add(['Grid', 'menu' => false]);
$m = $g->setModel(new Country($db));

$g->addJsPaginatorInContainer(30, 400);
