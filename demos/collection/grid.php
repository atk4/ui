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
$model->addUserAction('test', function (Model $model) {
    return 'test from ' . $model->getTitle() . ' was successful!';
});

$grid->setModel($model);

// add country flag column
$grid->addColumn('flag', [
    Table\Column\CountryFlag::class,
    'codeField' => $model->fieldName()->iso,
    'nameField' => $model->fieldName()->name,
]);

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
$grid->addExecutorButton($grid->getExecutorFactory()->createExecutor($model->getUserAction('test'), $grid));

$grid->addActionButton('Say HI', function (Jquery $j, $id) use ($grid) {
    $model = Country::assertInstanceOf($grid->model);

    return 'Loaded "' . $model->load($id)->name . '" from ID=' . $id;
});

$grid->addModalAction(['icon' => 'external'], 'Modal Test', function (View $p, $id) {
    Message::addTo($p, ['Clicked on ID=' . $id]);
});

// Creating an executor for delete action.
$deleteExecutor = $grid->getExecutorFactory()->createExecutor($model->getUserAction('delete'), $grid);
$deleteExecutor->onHook(BasicExecutor::HOOK_AFTER_EXECUTE, function () {
    return [
        (new Jquery())->closest('tr')->transition('fade left'),
        new JsToast('Simulating delete in demo mode.'),
    ];
});
// TODO button is added not only to the table rows, but also below the table!
// $grid->addExecutorButton($deleteExecutor, new Button(['icon' => 'times circle outline']));

$sel = $grid->addSelection();
// Executing a modal on a bulk selection
$callback = function (View $modal, ?Array $ids) use ($grid) {
    if (!$ids) {
        Message::addTo($modal, [
            'No records were selected.',
            'type' => 'error',
            'icon' => 'times',
        ]);
    } else {
        $msg = Message::addTo($modal, [
            'The selected records will be permanently deleted.',
            'type' => 'warning',
            'icon' => 'warning',
        ]);
        $msg->text->addParagraph('IDs to be deleted:');
        foreach ($ids as $id) {
            $msg->text->addParagraph($id);
        }
        $form = Form::addTo($modal);
        $form->buttonSave->set('Delete');
        $form->buttonSave->icon = 'trash';
        $form->onSubmit(function (Form $form) use ($grid, $ids) {
            // iterate trough the selected IDs and delete them
            $grid->model->atomic(function () use ($grid, $ids) {
                foreach ($ids as $id) {
                    $grid->model->delete($id);
                }
            });

            return new JsBlock([
                $grid->jsReload(),
                $form->jsSuccess(),
            ]);
        });
    }
};

$grid->addModalBulkAction(['Delete selected', 'icon' => 'trash', 'class.orange active' => true], $callback);

$grid->menu->addItem('show selection')
    ->on('click', new JsExpression(
        'alert(\'Selected: \' + [])',
        [$sel->jsChecked()]
    ));

// Setting ipp with an array will add an ItemPerPageSelector to paginator.
$grid->setIpp([10, 25, 50, 100]);
