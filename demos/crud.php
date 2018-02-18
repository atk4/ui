<?php

require 'init.php';
require 'database.php';

$g = $app->add(['CRUD', 'ipp'=>5]);
$g->setModel(new Country($db));

$app->add(['ui'=>'divider']);

$c = $app->add('Columns');
$cc = $c->addColumn(0, 'ui blue segment');

// CRUD can operate with various fields
$cc->add(['Header', 'Configured CRUD']);
$crud = $cc->add([
    'CRUD',
    'fieldsDefault'=> ['name'],
    'fieldsCreate' => ['iso', 'iso3', 'name', 'phonecode'],
    'ipp'          => 5,
]);

// Condition on the model can be applied after setting the model
$crud->setModel(new Country($app->db))->addCondition('numcode', '<', 200);

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
    'pageUpdate'      => ['\MyVP'],
    'ipp'             => 5,
]);

$crud->menu->addItem(['Rescan', 'icon'=>'recycle']);

// Condition on the model can be applied after setting the model
$crud->setModel(new File($app->db))->addCondition('parent_folder_id', null);
