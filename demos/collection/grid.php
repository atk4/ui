<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$grid = \Atk4\Ui\Grid::addTo($app);
$model = new CountryLock($app->db);
$model->addUserAction('test', function ($model) {
    return 'test from ' . $model->getTitle() . ' was successful!';
});

// Delete is already prevent by our lock Model, just simulating it.
$ex = new \Atk4\Ui\UserAction\JsCallbackExecutor();
$ex->onHook(\Atk4\Ui\UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, function () {
    return [
        (new \Atk4\Ui\Jquery())->closest('tr')->transition('fade left'),
        new \Atk4\Ui\JsToast('Simulating delete in demo mode.'),
    ];
});
$model->getUserAction('delete')->ui['executor'] = $ex;

$grid->setModel($model);

// Adding Quicksearch on Name field using auto query.
$grid->addQuickSearch(['name'], true);

$grid->menu->addItem(['Add Country', 'icon' => 'add square'], new \Atk4\Ui\JsExpression('alert(123)'));
$grid->menu->addItem(['Re-Import', 'icon' => 'power'], new \Atk4\Ui\JsReload($grid));
$grid->menu->addItem(['Delete All', 'icon' => 'trash', 'red active']);

$grid->addColumn(null, [\Atk4\Ui\Table\Column\Template::class, 'hello<b>world</b>']);

$grid->addActionButton('test');

$grid->addActionButton('Say HI', function ($j, $id) use ($grid) {
    return 'Loaded "' . $grid->model->load($id)->get('name') . '" from ID=' . $id;
});

$grid->addModalAction(['icon' => [\Atk4\Ui\Icon::class, 'external']], 'Modal Test', function ($p, $id) {
    \Atk4\Ui\Message::addTo($p, ['Clicked on ID=' . $id]);
});

    $grid->addActionButton(['icon' => 'delete'], $model->getUserAction('delete'));

$sel = $grid->addSelection();
$grid->menu->addItem('show selection')->on('click', new \Atk4\Ui\JsExpression(
    'alert("Selected: "+[])',
    [$sel->jsChecked()]
));

// Setting ipp with an array will add an ItemPerPageSelector to paginator.
$grid->setIpp([10, 25, 50, 100]);
