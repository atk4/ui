<?php

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

$g = $app->add('Grid');
$g->setModel(new Country($db));
$g->addJsSearch();

$g->ipp = 10;
