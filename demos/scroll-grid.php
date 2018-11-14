<?php


require 'init.php';
require 'database.php';

$app->add(['View', 'ui' => 'ui clearing divider']);

$g = $app->add(['Grid', 'menu' => false]);
$m = $g->setModel(new Country($db));

//$g->table->addTotals(['iso'=>['count']]); // of course this will not work

$g->addJsPaginator(30);
