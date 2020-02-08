<?php

require __DIR__ . '/init.php';
require __DIR__ . '/database.php';
$m = new Country($db);
$m->addAction('test', ['ui'=>['button'=>['icon'=>'pencil']]]);
$m->addAction('test1');
$m->addAction('test2');
//$m->getAction('edit')->system =true;
//$m->getAction('delete')->system =true;

$g = $app->add(['CRUD', 'ipp'=>10]);

// callback for model action add form.
$g->onFormAdd(function ($form, $t) {
    $form->js(true, $form->getField('name')->jsInput()->val('Entering value via javascript'));
});

// callback for model action edit form.
$g->onFormEdit(function ($form) {
    $form->js(true, $form->getField('name')->jsInput()->attr('readonly', true));
});

// callback for both model action edit and add.
$g->onFormAddEdit(function ($form, $ex) {
    $form->onSubmit(function ($f) use ($ex) {
        return [$ex->hide(), new \atk4\ui\jsToast('Submit all right! This demo does not saved data.')];
    });
});

$g->setModel($m);

$g->addDecorator($m->title_field, ['Link', ['test' => false, 'path' => 'interfaces/page'], ['_id'=>'id']]);

$app->add(['ui'=>'divider']);

$c = $app->add('Columns');
$cc = $c->addColumn(0, 'ui blue segment');

// CRUD can operate with various fields
$cc->add(['Header', 'Configured CRUD']);
$crud = $cc->add([
    'CRUD',
    //'fieldsCreate' => ['name', 'iso', 'iso3', 'numcode', 'phonecode'], // when creating then show more fields
    'displayFields'=> ['name'], // when updating then only allow to update name
    'editFields'   => ['name', 'iso', 'iso3'],
    'ipp'          => 5,
    'paginator'    => ['range'=>2, 'class'=>['blue inverted']],  // reduce range on the paginator
    'menu'         => ['class'=>['green inverted']],
    'table'        => ['class'=>['red inverted']],
]);
// Condition on the model can be applied on a model
$m = new Country($db);
$m->addCondition('numcode', '<', 200);
$m->addHook('validate', function ($m2, $intent) {
    $err = [];
    if ($m2->get('numcode') >= 200) {
        $err['numcode'] = 'Should be less than 200';
    }

    return $err;
});
$crud->setModel($m);

// Because CRUD inherits Grid, you can also define custom actions
$crud->addModalAction(['icon'=>'cogs'], 'Details', function ($p, $id) use ($crud) {
    $p->add(['Message', 'Details for: '.$crud->model->load($id)['name'].' (id: '.$id.')']);
});

$cc = $c->addColumn();
$cc->add(['Header', 'Cutomizations']);

class MyExecutor extends atk4\ui\ActionExecutor\UserAction
{
    public function addFormTo(\atk4\ui\View $view): \atk4\ui\Form
    {
        /** @var \atk4\ui\Columns $columns */
        $columns = $view->add('Columns');
        $left = $columns->addColumn();
        $right = $columns->addColumn();

        $result = parent::addFormTo($left); // TODO: Change the autogenerated stub

        if ($this->action->owner['is_folder']) {
            $right->add(['Grid', 'menu'=>false, 'ipp'=>5])
                ->setModel($this->action->owner->ref('SubFolder'));
        } else {
            $right->add(['Message', 'Not a folder', 'warning']);
        }

        return $result;
    }
}
$file = new File($db);
$file->getAction('edit')->ui['executor'] = MyExecutor::class;

$crud = $cc->add([
    'CRUD',
    'canCreate'       => false,
    'canDelete'       => false,
    //'pageUpdate'      => ['\MyVP'],
    'ipp'             => 5,
]);

$crud->menu->addItem(['Rescan', 'icon'=>'recycle']);

// Condition on the model can be applied after setting the model
$crud->setModel($file)->addCondition('parent_folder_id', null);
