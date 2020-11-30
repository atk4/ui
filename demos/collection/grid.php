<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Button;
use atk4\ui\UserAction\ExecutorFactory;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$grid = \atk4\ui\Grid::addTo($app);
$model = new CountryLock($app->db);
$model->addUserAction('test', function ($model) {
    return 'test from ' . $model->getTitle() . ' was successful!';
});

$grid->setModel($model);

// Adding Quicksearch on Name field using auto query.
$grid->addQuickSearch(['name'], true);

$grid->menu->addItem(['Add Country', 'icon' => 'add square'], new \atk4\ui\JsExpression('alert(123)'));
$grid->menu->addItem(['Re-Import', 'icon' => 'power'], new \atk4\ui\JsReload($grid));
$grid->menu->addItem(['Delete All', 'icon' => 'trash', 'red active']);

$grid->addColumn(null, [\atk4\ui\Table\Column\Template::class, 'hello<b>world</b>']);

// Creating a button for executing model test user action.
$grid->addExecutorButton($grid->executorFactory::create($model->getUserAction('test'), $grid));

$grid->addActionButton('Say HI', function ($j, $id) use ($grid) {
    return 'Loaded "' . $grid->model->load($id)->get('name') . '" from ID=' . $id;
});

$grid->addModalAction(['icon' => [\atk4\ui\Icon::class, 'external']], 'Modal Test', function ($p, $id) {
    \atk4\ui\Message::addTo($p, ['Clicked on ID=' . $id]);
});

// Creating an executor for delete action.
$deleteExecutor = ExecutorFactory::create($model->getUserAction('delete'), $grid);
$deleteExecutor->onHook(\atk4\ui\UserAction\BasicExecutor::HOOK_AFTER_EXECUTE, function () {
    return [
        (new \atk4\ui\Jquery())->closest('tr')->transition('fade left'),
        new \atk4\ui\JsToast('Simulating delete in demo mode.'),
    ];
});
$grid->addExecutorButton($deleteExecutor, new Button(['icon' => 'times circle outline']));

$sel = $grid->addSelection();
$grid->menu->addItem('show selection')->on('click', new \atk4\ui\JsExpression(
    'alert("Selected: "+[])',
    [$sel->jsChecked()]
));

// Setting ipp with an array will add an ItemPerPageSelector to paginator.
$grid->setIpp([10, 25, 50, 100]);
