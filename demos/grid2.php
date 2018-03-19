<?php

require 'init.php';
require 'database.php';

$g = $app->add(['Grid']);
$g->setModel(new Country($db));
$g->addQuickSearch();
//$g->addColumn('id');
$dragHandler = $g->addDragHandler();

$dragHandler->onReorder(function($newOrder) {
    return new \atk4\ui\jsNotify('New orders: '.implode(' - ', $newOrder));
});

$g->ipp = 10;
