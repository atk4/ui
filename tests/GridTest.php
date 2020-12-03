<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Ui\Table;

class GridTest extends \Atk4\Core\AtkPhpunit\TestCase
{
    use Concerns\HandlesTable;

    public $m;

    protected function setUp(): void
    {
        $a = [
            1 => ['id' => 1, 'email' => 'test@test.com', 'password' => 'abc123', 'xtra' => 'xtra'],
            2 => ['id' => 2, 'email' => 'test@yahoo.com', 'password' => 'secret'],
        ];
        $this->m = new MyModel(new \Atk4\Data\Persistence\Array_($a));
    }

    public function test1()
    {
        $t = new Table();
        $t->invokeInit();
        $t->setModel($this->m, false);

        $t->addColumn('email');
        $t->addColumn(null, new Table\Column\Template('password={$password}'));

        $this->assertSame('<td>{$email}</td><td>password={$password}</td>', $t->getDataRowHtml());
        $this->assertSame(
            '<tr data-id="1"><td>test@test.com</td><td>password=abc123</td></tr>',
            $this->extractTableRow($t)
        );
    }

    public function test1a()
    {
        $t = new Table();
        $t->invokeInit();
        $t->setModel($this->m, false);

        $t->addColumn('email');
        $t->addColumn('password');

        $this->assertSame('<td>{$email}</td><td>***</td>', $t->getDataRowHtml());
        $this->assertSame(
            '<tr data-id="1"><td>test@test.com</td><td>***</td></tr>',
            $this->extractTableRow($t)
        );
    }

    public function test2()
    {
        $t = new Table();
        $t->invokeInit();
        $t->setModel($this->m, ['email']);
        $del = $t->addColumn(null, [Table\Column\Delete::class]);

        $this->assertSame('<td>{$email}</td><td><a href="#" title="Delete {$email}?" class="' . $del->short_name . '"><i class="ui red trash icon"></i>Delete</a></td>', $t->getDataRowHtml());
        $this->assertSame(
            '<tr data-id="1"><td>test@test.com</td><td><a href="#" title="Delete test@test.com?" class="' . $del->short_name . '"><i class="ui red trash icon"></i>Delete</a></td></tr>',
            $this->extractTableRow($t)
        );
    }

    public function test3()
    {
        $t = new Table();
        $t->invokeInit();
        $t->setModel($this->m, ['email']);
        $t->addColumn('xtra', null, ['type' => 'password']);

        $this->assertSame('<td>{$email}</td><td>***</td>', $t->getDataRowHtml());
        $this->assertSame(
            '<tr data-id="1"><td>test@test.com</td><td>***</td></tr>',
            $this->extractTableRow($t)
        );
    }
}

class MyModel extends \Atk4\Data\Model
{
    public $title_field = 'email';

    protected function init(): void
    {
        parent::init();

        $this->addField('email');
        $this->addField('password', ['type' => 'password']);
    }
}
