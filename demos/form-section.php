<?php


require 'init.php';
require 'database.php';

$m = new Country($db);
$m->loadAny();

//Prevent form from saving,
$noSave = function () {
};

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

$f->onSubmit($noSave);

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

$f->onSubmit($noSave);

$app->add(['ui' => 'divider']);

////////////////////////////////

$f = $app->add('Form');
$f->setModel($m, false);

$v = $f->layout->addLayout();

$v->add(['Header', 'Tabs in Form']);
$v->setModel($m, ['name']);

$tabs = $f->layout->addLayout('Tabs');

$t1 = $tabs->addTab('Tab 1');
$t1->addGroup('In Group')->setModel($m, ['iso', 'iso3']);

$t2 = $tabs->addTab('Tab 2');
$t2->setModel($m, ['numcode', 'phonecode']);

$f->onSubmit($noSave);

$app->add(['ui' => 'divider']);

/////////////////////////////////////////

$app->add(['Header', 'Color in form']);

$f = $app->add('Form');
$f->setModel($m, false);

$v = $f->layout->addLayout(['View', 'ui' => 'segment red inverted'], false);

$v->add(['View', 'This section in Red', 'ui' => 'dividing header', 'element' => 'h2']);
$v->setModel($m, ['name']);

$v = $f->layout->addLayout(['View', 'ui' => 'segment teal inverted']);
$cols = $v->addLayout('Columns');

$c1 = $cols->addColumn();
$c1->setModel($m, ['iso', 'iso3']);

$c2 = $cols->addColumn();
$c2->setModel($m, ['numcode', 'phonecode']);

$f->onSubmit($noSave);

$app->add(['ui' => 'divider']);
