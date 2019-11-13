<?php

require 'init.php';
require 'database.php';
$m = new Country($db);
//$m->getAction('edit')->system =true;
//$m->getAction('delete')->system =true;

$g = $app->add(['CRUD', 'ipp'=>5]);
$g->setModel($m);

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

class MyVP extends \atk4\ui\VirtualPage
{
    public $l;
    public $r;
    public $f;

    public function init()
    {
        parent::init();

        $col = parent::add('Columns');
        $this->l = $col->addColumn();
        $this->r = $col->addColumn();
    }

    public function add($seed, $arg = null)
    {
        return $this->f = $this->l->add($seed, $arg);
    }

    public function renderView()
    {
        if ($this->f instanceof \atk4\ui\Form) {
            if ($this->f->model['is_folder']) {
                $this->r->add(['Grid', 'menu'=>false, 'ipp'=>5])
                    ->setModel($this->f->model->ref('SubFolder'));
            } else {
                $this->r->add(['Message', 'Not a folder', 'warning']);
            }
        }

        return parent::renderView();
    }
}

$crud = $cc->add([
    'CRUD',
    'canCreate'       => false,
    'canDelete'       => false,
    //'pageUpdate'      => ['\MyVP'],
    'ipp'             => 5,
]);

$crud->menu->addItem(['Rescan', 'icon'=>'recycle']);

// Condition on the model can be applied after setting the model
$crud->setModel(new File($db))->addCondition('parent_folder_id', null);
