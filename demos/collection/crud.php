<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Columns;
use Atk4\Ui\Crud;
use Atk4\Ui\Form;
use Atk4\Ui\Grid;
use Atk4\Ui\Header;
use Atk4\Ui\Message;
use Atk4\Ui\Table;
use Atk4\Ui\UserAction\ModalExecutor;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new Country($app->db);

$crud = Crud::addTo($app, ['ipp' => 10]);

// callback for model action add form
$crud->onFormAdd(static function (Form $form, ModalExecutor $ex) use ($model) {
    $form->js(true, $form->getControl($model->fieldName()->name)->jsInput()->val('Entering value via javascript'));
});

// callback for model action edit form
$crud->onFormEdit(static function (Form $form) use ($model) {
    $form->js(true, $form->getControl($model->fieldName()->name)->jsInput()->attr('readonly', true));
});

$crud->setModel($model);

$crud->addDecorator($model->titleField, [Table\Column\Link::class, ['test' => false, 'path' => 'interfaces/page'], ['_id' => $model->fieldName()->id]]);

View::addTo($app, ['ui' => 'divider']);

$columns = Columns::addTo($app);
$column = $columns->addColumn();

// Crud can operate with various fields
Header::addTo($column, ['Configured Crud']);
$crud = Crud::addTo($column, [
    'displayFields' => [$model->fieldName()->name], // field to display in Crud
    'editFields' => [$model->fieldName()->name, $model->fieldName()->iso, $model->fieldName()->iso3], // field to display on 'edit' action
    'ipp' => 5,
    'paginator' => ['range' => 2, 'class' => ['blue inverted']], // reduce range on the paginator
    'menu' => ['class' => ['green inverted']],
    'table' => ['class' => ['red inverted']],
]);
// condition on the model can be applied on a model
$model = new Country($app->db);
$model->addCondition($model->fieldName()->numcode, '<', 200);
$model->onHook(Model::HOOK_VALIDATE, static function (Country $model, ?string $intent) {
    $err = [];
    if ($model->numcode >= 200) {
        $err[$model->fieldName()->numcode] = 'Should be less than 200';
    }

    return $err;
});
$crud->setModel($model);

// because Crud inherits Grid, you can also define custom actions
$crud->addModalAction(['icon' => 'cogs'], 'Details', static function (View $p, $id) use ($crud) {
    $model = Country::assertInstanceOf($crud->model);
    Message::addTo($p, ['Details for: ' . $model->load($id)->name . ' (id: ' . $id . ')']);
});

$column = $columns->addColumn();
Header::addTo($column, ['Customizations']);

$myExecutorClass = AnonymousClassNameCache::get_class(fn () => new class() extends ModalExecutor {
    public function addFormTo(View $view): Form
    {
        $columns = Columns::addTo($view);
        $left = $columns->addColumn();
        $right = $columns->addColumn();

        $result = parent::addFormTo($left);

        if (File::assertInstanceOf($this->action->getEntity())->is_folder) {
            Grid::addTo($right, ['menu' => false, 'ipp' => 5])
                ->setModel(File::assertInstanceOf($this->getAction()->getModel())->SubFolder);
        } else {
            Message::addTo($right, ['Not a folder', 'type' => 'warning']);
        }

        return $result;
    }
});

$file = new File($app->db);
$app->getExecutorFactory()->registerExecutor($file->getUserAction('edit'), [$myExecutorClass]);

$crud = Crud::addTo($column, [
    'ipp' => 5,
]);

$crud->menu->addItem(['Rescan', 'icon' => 'recycle']);

// condition on the model can be applied after setting the model
$crud->setModel($file);
$file->addCondition($file->fieldName()->parent_folder_id, null);
