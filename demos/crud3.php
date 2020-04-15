<?php

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

class TestModel extends \atk4\data\Model
{
    public $table = 'test';

    public function init(): void
    {
        parent::init();

        $this->addField('name');
        $this->addField('code');
        $this->addField('country');
    }
}

// Prepare Persistence and data Model
$data = ['test' => [
    1 => ['id'=>1, 'name'=>'ABC9', 'code'=>11, 'country'=>'Ireland'],
    2 => ['id'=>2, 'name'=>'ABC8', 'code'=>12, 'country'=>'Ireland'],
    3 => ['id'=>3, 'name'=>'ABC7', 'code'=>13, 'country'=>'Latvia'],
    4 => ['id'=>4, 'name'=>'ABC6', 'code'=>14, 'country'=>'UK'],
    5 => ['id'=>5, 'name'=>'ABC5', 'code'=>15, 'country'=>'UK'],
    6 => ['id'=>6, 'name'=>'ABC4', 'code'=>16, 'country'=>'Ireland'],
    7 => ['id'=>7, 'name'=>'ABC3', 'code'=>17, 'country'=>'Latvia'],
    8 => ['id'=>8, 'name'=>'ABC2', 'code'=>18, 'country'=>'Russia'],
    9 => ['id'=>9, 'name'=>'ABC1', 'code'=>19, 'country'=>'Latvia'],
]];
$p = new \atk4\data\Persistence\Array_($data);
$m = new TestModel($p);

// add CRUD
\atk4\ui\Header::addTo($app, ['CRUD with Array Persistence']);
$c = \atk4\ui\CRUD::addTo($app, ['ipp'=>5]);
$c->setModel($m);
