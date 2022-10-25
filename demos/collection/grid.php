<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Grid;
use Atk4\Ui\Jquery;
use Atk4\Ui\JsExpression;
use Atk4\Ui\JsReload;
use Atk4\Ui\JsToast;
use Atk4\Ui\Message;
use Atk4\Ui\Table;
use Atk4\Ui\UserAction\BasicExecutor;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$grid = Grid::addTo($app);
$model = new Country($app->db);
$model->addUserAction('test', function (Model $model) {
    return 'test from ' . $model->getTitle() . ' was successful!';
});

$grid->setModel($model);

// Adding Quicksearch on Name field using auto query.
$grid->addQuickSearch([$model->fieldName()->name], true);

if ($grid->stickyGet('no-ajax')) {
    $grid->quickSearch->useAjax = false;
}

$grid->menu->addItem(['Add Country', 'icon' => 'add square'], new JsExpression('alert(123)'));
$grid->menu->addItem(['Re-Import', 'icon' => 'power'], new JsReload($grid));
$grid->menu->addItem(['Delete All', 'icon' => 'trash', 'class.red active' => true]);

$grid->addColumn(null, [Table\Column\Template::class, 'hello<b>world</b>']);

// Creating a button for executing model test user action.
$grid->addExecutorButton($grid->getExecutorFactory()->create($model->getUserAction('test'), $grid));

$grid->addActionButton('Say HI', function (Jquery $j, $id) use ($grid) {
    $model = Country::assertInstanceOf($grid->model);

    return 'Loaded "' . $model->load($id)->name . '" from ID=' . $id;
});

$grid->addModalAction(['icon' => 'external'], 'Modal Test', function (View $p, $id) {
    Message::addTo($p, ['Clicked on ID=' . $id]);
});

// Creating an executor for delete action.
$deleteExecutor = $grid->getExecutorFactory()->create($model->getUserAction('delete'), $grid);
$deleteExecutor->onHook(BasicExecutor::HOOK_AFTER_EXECUTE, function () {
    return [
        (new Jquery())->closest('tr')->transition('fade left'),
        new JsToast('Simulating delete in demo mode.'),
    ];
});
// TODO button is added not only to the table rows, but also below the table!
// $grid->addExecutorButton($deleteExecutor, new Button(['icon' => 'times circle outline']));

$sel = $grid->addSelection();
$grid->menu->addItem('show selection')->on('click', new JsExpression(
    'alert(\'Selected: \' + [])',
    [$sel->jsChecked()]
));

// Setting ipp with an array will add an ItemPerPageSelector to paginator.
$grid->setIpp([10, 25, 50, 100]);
