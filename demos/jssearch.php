<?php

require 'init.php';
require 'database.php';

$g = $app->add('Grid');
$g->setModel(new Country($db));
$g->addJsSearch();

$g->ipp = 10;
