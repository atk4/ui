<?php

require 'init.php';
require 'database.php';

$app->add(['Button', 'Dynamic scroll in CRUD and Grid', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['scroll-grid']);
$app->add(['View', 'ui' => 'ui clearing divider']);

$app->add(['Header', 'Dynamic scroll in Grid with fixed column headers']);

$c = $app->add('Columns');

$c1 = $c->addColumn();
$g1 = $c1->add(['CRUD']);
$m1 = $g1->setModel(new Country($db));//, ['name', 'iso']);
$g1->addQuickSearch(['name', 'iso']);

// demo for additional action buttons in CRUD + jsPaginator
$g1->addModalAction(['icon'=>'cogs'], 'Details', function ($p, $id) use ($g1) {
    $p->add(['Card'])->setModel($g1->model->load($id));
});
$g1->addAction('red', function ($js) {
    return $js->closest('tr')->css('color', 'red');
});
// THIS SHOULD GO AFTER YOU CALL addAction() !!!
$g1->addJsPaginatorInContainer(30, 350);

$c2 = $c->addColumn();
$g2 = $c2->add(['Grid', 'menu' => false]);
$m2 = $g2->setModel(new Country($db));
$g2->addJsPaginatorInContainer(20, 200);

$g3 = $c2->add(['Grid', 'menu' => false]);
$m3 = $g3->setModel(new Country($db));
$g3->addJsPaginatorInContainer(10, 150);
