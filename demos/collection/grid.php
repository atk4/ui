<?php

chdir('..');
require_once 'init.php';
require_once 'database.php';

$g = \atk4\ui\Grid::addTo($app);
$m = new Country($db);
$m->addAction('test', function ($m) {
    return 'test from ' . $m->getTitle() . ' was successful!';
});
$g->setModel($m);

//Adding Quicksearch on Name field using auto query.
$g->addQuickSearch(['name'], true);

$g->menu->addItem(['Add Country', 'icon' => 'add square'], new \atk4\ui\jsExpression('alert(123)'));
$g->menu->addItem(['Re-Import', 'icon' => 'power'], new \atk4\ui\jsReload($g));
$g->menu->addItem(['Delete All', 'icon' => 'trash', 'red active']);

$g->addColumn(null, ['Template', 'hello<b>world</b>']);
//$g->addColumn('name', ['TableColumn/Link', 'page2']);
$g->addColumn(null, 'Delete');

$g->addActionButton('test');

$g->addActionButton('Say HI', function ($j, $id) use ($g) {
    return 'Loaded "' . $g->model->load($id)['name'] . '" from ID=' . $id;
});

$g->addModalAction(['icon'=>'external'], 'Modal Test', function ($p, $id) {
    \atk4\ui\Message::addTo($p, ['Clicked on ID=' . $id]);
});

$sel = $g->addSelection();
$g->menu->addItem('show selection')->on('click', new \atk4\ui\jsExpression(
    'alert("Selected: "+[])',
    [$sel->jsChecked()]
));

//Setting ipp with an array will add an ItemPerPageSelector to paginator.
$g->setIpp([10, 25, 50, 100]);
