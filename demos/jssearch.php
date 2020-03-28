<?php

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

$g = \atk4\ui\Grid::addTo($app);
$g->setModel(new Country($db));
$g->addJsSearch();

$g->ipp = 10;
