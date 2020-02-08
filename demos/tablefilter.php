<?php

require __DIR__ . '/init.php';
require __DIR__ . '/database.php';

//For popup positioning to work correctly, table need to be inside a view segment.
$view = $app->add('View', ['ui' => 'basic segment']);
$g = $view->add(['Grid']);

$m = new Country($db);
$m->addExpression('is_uk', 'if([iso] = "GB", 1, 0)')->type = 'boolean';

$g->setModel($m);
$g->addFilterColumn();

$g->ipp = 20;
