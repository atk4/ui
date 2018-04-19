<?php

require 'init.php';
require 'database.php';

//For popup positioning to work correctly, table need to be inside a view segment.
$view = $app->add('View', ['ui' => 'basic segment']);
$g = $view->add(['Grid']);
$g->setModel(new Country($db));

$g->addFilterColumn(/*['name']*/);
$g->ipp = 10;
