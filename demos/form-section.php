<?php


require 'init.php';
require 'database.php';

$m = new Country($db);
$m->loadAny();

$f = $app->add('Form');
$f->setModel($m, false);

$v = $f->layout->addLayout();

$v->add(['Header', 'Column Section in Form']);
$v->setModel($m, ['name']);

$cols = $f->layout->addLayout('Columns');

$c1 = $cols->addColumn();
$c1->setModel($m, ['iso', 'iso3']);

$c2 = $cols->addColumn();
$c2->setModel($m, ['numcode', 'phonecode']);

$app->add(['ui' => 'divider']);

////////////////////////////////

$f = $app->add('Form');
$f->setModel($m, false);

$v = $f->layout->addLayout();

$v->add(['Header', 'Accordion Section in Form']);
$v->setModel($m, ['name']);

$acc = $f->layout->addLayout('Accordion');

$a1 = $acc->addSection('Section 1');
$a1->setModel($m, ['iso', 'iso3']);

$a2 = $acc->addSection('Section 2');
$a2->setModel($m, ['numcode', 'phonecode']);

$app->add(['ui' => 'divider']);

////////////////////////////////

$f = $app->add('Form');
$f->setModel($m, false);

$v = $f->layout->addLayout();

$v->add(['Header', 'Tabs in Form']);
$v->setModel($m, ['name']);

$tabs = $f->layout->addLayout('Tabs');

$t1 = $tabs->addTab('Tab 1');
$t1->setModel($m, ['iso', 'iso3']);

$t2 = $tabs->addTab('Tab 2');
$t2->setModel($m, ['numcode', 'phonecode']);
