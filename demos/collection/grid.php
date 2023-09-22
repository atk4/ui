<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\Grid;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsExpression;
use Atk4\Ui\Js\JsReload;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\Message;
use Atk4\Ui\Table;
use Atk4\Ui\UserAction\BasicExecutor;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$grid = Grid::addTo($app);
$model = new Country($app->db);
$model->addUserAction('test', static function (Model $model) {
    return 'test from ' . $model->getTitle() . ' was successful!';
});

$grid->setModel($model);

// add country flag column
$grid->addColumn('flag', [
    Table\Column\CountryFlag::class,
    'codeField' => $model->fieldName()->iso,
    'nameField' => $model->fieldName()->name,
]);

// adding Quicksearch on Name field using auto query
$grid->addQuickSearch([$model->fieldName()->name], true);

if ($grid->stickyGet('no-ajax')) {
    $grid->quickSearch->useAjax = false;
}

$grid->menu->addItem(['Add Country', 'icon' => 'add square'], new JsExpression('alert(123)'));
$grid->menu->addItem(['Re-Import', 'icon' => 'power'], new JsReload($grid));
$grid->menu->addItem(['Delete All', 'icon' => 'trash', 'class.red active' => true]);

$grid->addColumn(null, [Table\Column\Template::class, 'hello<b>world</b>']);

// creating a button for executing model test user action
$grid->addExecutorButton($grid->getExecutorFactory()->createExecutor($model->getUserAction('test'), $grid));

$grid->addActionButton('Say HI', static function (Jquery $j, $id) use ($grid) {
    $model = Country::assertInstanceOf($grid->model);

    return 'Loaded "' . $model->load($id)->name . '" from ID=' . $id;
});

$grid->addModalAction(['icon' => 'external'], 'Modal Test', static function (View $p, $id) {
    Message::addTo($p, ['Clicked on ID=' . $id]);
});

// creating an executor for delete action
$deleteExecutor = $grid->getExecutorFactory()->createExecutor($model->getUserAction('delete'), $grid);
$deleteExecutor->onHook(BasicExecutor::HOOK_AFTER_EXECUTE, static function () {
    return [
        (new Jquery())->closest('tr')->transition('fade left'),
        new JsToast('Simulating delete in demo mode.'),
    ];
});
// TODO button is added not only to the table rows, but also below the table!
// $grid->addExecutorButton($deleteExecutor, new Button(['icon' => 'times circle outline']));

$grid->addSelection();

$grid->addBulkAction(['Show selected', 'icon' => 'binoculars'], static function (Jquery $j, array $ids) {
    return new JsToast('Selected: ' . implode(', ', $ids) . '#');
});

// executing a modal on a bulk selection
$grid->addModalBulkAction(['Delete selected', 'icon' => 'trash'], '', static function (View $modal, array $ids) use ($grid) {
    Message::addTo($modal, [
        'The selected records will be permanently deleted: ' . implode(', ', $ids) . '#',
        'type' => 'warning',
        'icon' => 'warning',
    ]);
    $form = Form::addTo($modal);
    $form->buttonSave->set('Delete');
    $form->buttonSave->icon = 'trash';
    $form->onSubmit(static function (Form $form) use ($grid, $ids) {
        $grid->model->atomic(static function () use ($grid, $ids) {
            foreach ($ids as $id) {
                $grid->model->delete($id);
            }
        });

        return new JsBlock([
            $grid->jsReload(),
            $form->jsSuccess(),
        ]);
    });
});

// setting ipp with an array will add an ItemPerPageSelector to paginator
$grid->setIpp([10, 100, 1000]);
