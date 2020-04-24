<?php

chdir('..');
require_once 'atk-init.php';

\atk4\ui\Button::addTo($app, ['Dynamic scroll in CRUD and Grid', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['scroll-grid']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Dynamic scroll in Grid with fixed column headers']);

$c = \atk4\ui\Columns::addTo($app);

$c1 = $c->addColumn();
$g1 = \atk4\ui\CRUD::addTo($c1);
$m1 = $g1->setModel(new Country($db)); //, ['name', 'iso']);
$g1->addQuickSearch(['name', 'iso']);

// demo for additional action buttons in CRUD + jsPaginator
$g1->addModalAction(['icon'=>'cogs'], 'Details', function ($p, $id) use ($g1) {
    \atk4\ui\Card::addTo($p)->setModel($g1->model->load($id));
});
$g1->addActionButton('red', function ($js) {
    return $js->closest('tr')->css('color', 'red');
});
// THIS SHOULD GO AFTER YOU CALL addAction() !!!
$g1->addJsPaginatorInContainer(30, 350);

$c2 = $c->addColumn();
$g2 = \atk4\ui\Grid::addTo($c2, ['menu' => false]);
$m2 = $g2->setModel(new Country($db));
$g2->addJsPaginatorInContainer(20, 200);

$g3 = \atk4\ui\Grid::addTo($c2, ['menu' => false]);
$m3 = $g3->setModel(new Country($db));
$g3->addJsPaginatorInContainer(10, 150);
