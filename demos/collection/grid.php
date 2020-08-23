<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$grid = \atk4\ui\Grid::addTo($app);
$model = new CountryLock($app->db);
$model->addUserAction('test', function ($model) {
    return 'test from ' . $model->getTitle() . ' was successful!';
});

// Delete is already prevent by our lock Model, just simulating it.
$ex = new \atk4\ui\UserAction\JsCallbackExecutor();
$ex->onHook(\atk4\ui\UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, function () {
    return [
        (new \atk4\ui\Jquery())->closest('tr')->transition('fade left'),
        new \atk4\ui\JsToast('Simulating delete in demo mode.'),
    ];
});
$model->getUserAction('delete')->ui['executor'] = $ex;

$grid->setModel($model);

// Adding Quicksearch on Name field using auto query.
$grid->addQuickSearch(['name'], true);

$grid->menu->addItem(['Add Country', 'icon' => 'add square'], new \atk4\ui\JsExpression('alert(123)'));
$grid->menu->addItem(['Re-Import', 'icon' => 'power'], new \atk4\ui\JsReload($grid));
$grid->menu->addItem(['Delete All', 'icon' => 'trash', 'red active']);

$grid->addColumn(null, [\atk4\ui\Table\Column\Template::class, 'hello<b>world</b>']);

$grid->addActionButton('test');

$grid->addActionButton('Say HI', function ($j, $id) use ($grid) {
    return 'Loaded "' . $grid->model->load($id)->get('name') . '" from ID=' . $id;
});

$grid->addModalAction(['icon' => [\atk4\ui\Icon::class, 'external']], 'Modal Test', function ($p, $id) {
    \atk4\ui\Message::addTo($p, ['Clicked on ID=' . $id]);
});

    $grid->addActionButton(['icon' => 'delete'], $model->getUserAction('delete'));

$sel = $grid->addSelection();
$grid->menu->addItem('show selection')->on('click', new \atk4\ui\JsExpression(
    'alert("Selected: "+[])',
    [$sel->jsChecked()]
));

// Setting ipp with an array will add an ItemPerPageSelector to paginator.
$grid->setIpp([10, 25, 50, 100]);
