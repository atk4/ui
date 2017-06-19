<?php

namespace atk4\ui\tests;

use atk4\ui\Table;
use atk4\ui\TableColumn\Template;

class ButtonTest extends \atk4\core\PHPUnit_AgileTestCase
{
    public $m;

    public function setUp()
    {
        $a = [];
        $a[1] = ['email'=>'test@test.com', 'password'=>'abc123'];
        $a[2] = ['email'=>'test@yahoo.com', 'password'=>'secret'];

        $this->m = new MyModel(new \atk4\data\Persistence_Array($a));
    }

    public function test1()
    {
        $t = new Table();
        $t->addColumn('hello');
        $t->addColumn(new Template('password={$password}'));
    }

    public function test2()
    {
        $t = new Table();
        $t->setModel($this->m);

        //$t->addColumn('hello');
    }

    public function extract($t)
    {
        // extract only <tr> out
        $val = $t->render();
        preg_match('/<.*data-id="1".*/m', $val, $matches);

        var_dump($matches[0]);

        return $matches[0];
    }
}

class MyModel extends \atk4\data\Model
{
    public function init()
    {
        parent::init();

        $this->addField('email');
        $this->addField('password', ['type'=>'password']);
    }
}
