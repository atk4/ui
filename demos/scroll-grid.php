<?php


require 'init.php';
require 'database.php';

$app->add(['View', 'ui' => 'ui clearing divider']);

$g = $app->add(['Grid']);
$m = $g->setModel(new Country($db));
$g->addJsPaginator(30);
