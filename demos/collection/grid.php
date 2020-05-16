<?php

namespace atk4\ui\demo;

require_once __DIR__ . '/../atk-init.php';

$g = \atk4\ui\Grid::addTo($app);
$m = new CountryLock($db);
$m->addAction('test', function ($m) {
    return 'test from ' . $m->getTitle() . ' was successful!';
});

// Delete is already prevent by our lock Model, just simulating it.
$ex = new \atk4\ui\ActionExecutor\jsUserAction();
$ex->onHook('afterExecute', function () {
    return [
        (new \atk4\ui\jQuery())->closest('tr')->transition('fade left'),
        new \atk4\ui\jsToast('Simulating delete in demo mode.'),
    ];
});
$m->getAction('delete')->ui['executor'] = $ex;

$g->setModel($m);

//Adding Quicksearch on Name field using auto query.
$g->addQuickSearch(['name'], true);

$g->menu->addItem(['Add Country', 'icon' => 'add square'], new \atk4\ui\jsExpression('alert(123)'));
$g->menu->addItem(['Re-Import', 'icon' => 'power'], new \atk4\ui\jsReload($g));
$g->menu->addItem(['Delete All', 'icon' => 'trash', 'red active']);

$g->addColumn(null, ['Template', 'hello<b>world</b>']);

$g->addActionButton('test');

$g->addActionButton('Say HI', function ($j, $id) use ($g) {
    return 'Loaded "' . $g->model->load($id)['name'] . '" from ID=' . $id;
});

$g->addModalAction(['icon' => 'external'], 'Modal Test', function ($p, $id) {
    \atk4\ui\Message::addTo($p, ['Clicked on ID=' . $id]);
});

$g->addActionButton(['icon' => 'delete'], $m->getAction('delete'));

$sel = $g->addSelection();
$g->menu->addItem('show selection')->on('click', new \atk4\ui\jsExpression(
    'alert("Selected: "+[])',
    [$sel->jsChecked()]
));

//Setting ipp with an array will add an ItemPerPageSelector to paginator.
$g->setIpp([10, 25, 50, 100]);
