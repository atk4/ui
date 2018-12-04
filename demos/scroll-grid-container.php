<?php

require 'init.php';
require 'database.php';

$app->add(['Button', 'Dynamic scroll in Grid', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['scroll-grid']);
$app->add(['View', 'ui' => 'ui clearing divider']);

$app->add(['Header', 'Dynamic scroll in Grid with fixed column headers']);

$c = $app->add('Columns');

$c1 = $c->addColumn();
$g1 = $c1->add(['Grid', 'menu' => false]);
$m1 = $g1->setModel(new Country($db));
$g1->addJsPaginatorInContainer(30, 400);

$c2 = $c->addColumn();
$g2 = $c2->add(['Grid', 'menu' => false]);
$m2 = $g2->setModel(new Country($db));
$g2->addJsPaginatorInContainer(20, 200);

$g3 = $c2->add(['Grid', 'menu' => false]);
$m3 = $g3->setModel(new Country($db));
$g3->addJsPaginatorInContainer(10, 150);
