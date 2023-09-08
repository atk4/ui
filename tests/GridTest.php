<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Table;

class GridTest extends TestCase
{
    use CreateAppTrait;
    use TableTestTrait;

    /** @var MyModel */
    public $m;

    protected function setUp(): void
    {
        parent::setUp();

        $a = [
            1 => ['id' => 1, 'email' => 'test@test.com', 'password' => 'abc123', 'xtra' => 'xtra'],
            2 => ['id' => 2, 'email' => 'test@yahoo.com', 'password' => 'secret'],
        ];
        $this->m = new MyModel(new Persistence\Array_($a));
    }

    public function test1(): void
    {
        $t = new Table();
        $t->setApp($this->createApp());
        $t->invokeInit();
        $t->setModel($this->m, []);

        $t->addColumn('email');
        $t->addColumn(null, new Table\Column\Template('password={$password}'));

        static::assertSame('<td>{$email}</td><td>password={$password}</td>', $t->getDataRowHtml());
        static::assertSame(
            '<tr data-id="1"><td>test@test.com</td><td>password=abc123</td></tr>',
            $this->extractTableRow($t)
        );
    }

    public function test2(): void
    {
        $t = new Table();
        $t->setApp($this->createApp());
        $t->invokeInit();
        $t->setModel($this->m, []);

        $t->addColumn('email');
        $t->addColumn('password', [Table\Column\Password::class]);

        static::assertSame('<td>{$email}</td><td>***</td>', $t->getDataRowHtml());
        static::assertSame(
            '<tr data-id="1"><td>test@test.com</td><td>***</td></tr>',
            $this->extractTableRow($t)
        );
    }
}

class MyModel extends Model
{
    public ?string $titleField = 'email';

    protected function init(): void
    {
        parent::init();

        $this->addField('email');
        $this->addField('password');
    }
}
