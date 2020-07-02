<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new CountryLock($app->db);

$g = \atk4\ui\CRUD::addTo($app, ['ipp' => 10]);

// callback for model action add form.
$g->onFormAdd(function ($form, $t) {
    $form->js(true, $form->getControl('name')->jsInput()->val('Entering value via javascript'));
});

// callback for model action edit form.
$g->onFormEdit(function ($form) {
    $form->js(true, $form->getControl('name')->jsInput()->attr('readonly', true));
});

// callback for both model action edit and add.
$g->onFormAddEdit(function ($form, $ex) {
    $form->onSubmit(function (\atk4\ui\Form $form) use ($ex) {
        return [$ex->hide(), new \atk4\ui\jsToast('Submit all right! This demo does not saved data.')];
    });
});

$g->setModel($model);

$g->addDecorator($model->title_field, [\atk4\ui\Table\Column\Link::class, ['test' => false, 'path' => 'interfaces/page'], ['_id' => 'id']]);

\atk4\ui\View::addTo($app, ['ui' => 'divider']);

$c = \atk4\ui\Columns::addTo($app);
$cc = $c->addColumn(0, 'ui blue segment');

// CRUD can operate with various fields
\atk4\ui\Header::addTo($cc, ['Configured CRUD']);
$crud = \atk4\ui\CRUD::addTo($cc, [
    //'fieldsCreate' => ['name', 'iso', 'iso3', 'numcode', 'phonecode'], // when creating then show more fields
    'displayFields' => ['name'], // when updating then only allow to update name
    'editFields' => ['name', 'iso', 'iso3'],
    'ipp' => 5,
    'paginator' => ['range' => 2, 'class' => ['blue inverted']],  // reduce range on the paginator
    'menu' => ['class' => ['green inverted']],
    'table' => ['class' => ['red inverted']],
]);
// Condition on the model can be applied on a model
$model = new CountryLock($app->db);
$model->addCondition('numcode', '<', 200);
$model->onHook(\atk4\data\Model::HOOK_VALIDATE, function ($model, $intent) {
    $err = [];
    if ($model->get('numcode') >= 200) {
        $err['numcode'] = 'Should be less than 200';
    }

    return $err;
});
$crud->setModel($model);

// Because CRUD inherits Grid, you can also define custom actions
$crud->addModalAction(['icon' => [\atk4\ui\Icon::class, 'cogs']], 'Details', function ($p, $id) use ($crud) {
    \atk4\ui\Message::addTo($p, ['Details for: ' . $crud->model->load($id)['name'] . ' (id: ' . $id . ')']);
});

$cc = $c->addColumn();
\atk4\ui\Header::addTo($cc, ['Cutomizations']);

/** @var \atk4\ui\UserAction\ModalExecutor $myExecutorClass */
$myExecutorClass = get_class(new class() extends \atk4\ui\UserAction\ModalExecutor {
    public function addFormTo(\atk4\ui\View $view): \atk4\ui\Form
    {
        $columns = \atk4\ui\Columns::addTo($view);
        $left = $columns->addColumn();
        $right = $columns->addColumn();

        $result = parent::addFormTo($left); // TODO: Change the autogenerated stub

        if ($this->action->owner['is_folder']) {
            \atk4\ui\Grid::addTo($right, ['menu' => false, 'ipp' => 5])
                ->setModel($this->action->owner->ref('SubFolder'));
        } else {
            \atk4\ui\Message::addTo($right, ['Not a folder', 'warning']);
        }

        return $result;
    }
});

$file = new FileLock($app->db);
$file->getUserAction('edit')->ui['executor'] = [$myExecutorClass];

$crud = \atk4\ui\CRUD::addTo($cc, [
    'ipp' => 5,
]);

$crud->menu->addItem(['Rescan', 'icon' => 'recycle']);

// Condition on the model can be applied after setting the model
$crud->setModel($file)->addCondition('parent_folder_id', null);
