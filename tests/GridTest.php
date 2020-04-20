<?php

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;
use atk4\ui\Table;
use atk4\ui\TableColumn\Template;

class GridTest extends AtkPhpunit\TestCase
{
    use Concerns\HandlesTable;

    public $m;

    public function setUp(): void
    {
        $a = [];
        $a[1] = ['id' => 1, 'email' => 'test@test.com', 'password' => 'abc123', 'xtra' => 'xtra'];
        $a[2] = ['id' => 2, 'email' => 'test@yahoo.com', 'password' => 'secret'];

        $this->m = new MyModel(new \atk4\data\Persistence\Array_($a));
    }

    public function test1()
    {
        $t = new Table();
        $t->init();
        $t->setModel($this->m, false);

        $t->addColumn('email');
        $t->addColumn(null, new Template('password={$password}'));

        $this->assertEquals('<td>{$email}</td><td>password={$password}</td>', $t->getDataRowHTML());
        $this->assertEquals(
            '<tr data-id="1"><td>test@test.com</td><td>password=abc123</td></tr>',
            $this->extractTableRow($t)
        );
    }

    public function test1a()
    {
        $t = new Table();
        $t->init();
        $t->setModel($this->m, false);

        $t->addColumn('email');
        $t->addColumn('password');

        $this->assertEquals('<td>{$email}</td><td>***</td>', $t->getDataRowHTML());
        $this->assertEquals(
            '<tr data-id="1"><td>test@test.com</td><td>***</td></tr>',
            $this->extractTableRow($t)
        );
    }

    public function test2()
    {
        $t = new Table();
        $t->init();
        $t->setModel($this->m, ['email']);
        $t->addColumn(null, 'Delete');

        $this->assertEquals('<td>{$email}</td><td><a href="#" title="Delete {$email}?" class="delete"><i class="ui red trash icon"></i>Delete</a></td>', $t->getDataRowHTML());
        $this->assertEquals(
            '<tr data-id="1"><td>test@test.com</td><td><a href="#" title="Delete test@test.com?" class="delete"><i class="ui red trash icon"></i>Delete</a></td></tr>',
            $this->extractTableRow($t)
        );
    }

    public function test3()
    {
        $t = new Table();
        $t->init();
        $t->setModel($this->m, ['email']);
        $t->addColumn('xtra', null, ['type' => 'password']);

        $this->assertEquals('<td>{$email}</td><td>***</td>', $t->getDataRowHTML());
        $this->assertEquals(
            '<tr data-id="1"><td>test@test.com</td><td>***</td></tr>',
            $this->extractTableRow($t)
        );
    }
}

class MyModel extends \atk4\data\Model
{
    public $title_field = 'email';

    public function init(): void
    {
        parent::init();

        $this->addField('email');
        $this->addField('password', ['type' => 'password']);
    }
}
