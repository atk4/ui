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
    protected $m;

    protected function setUp(): void
    {
        parent::setUp();

        $this->m = new MyModel(new Persistence\Array_([
            1 => ['id' => 1, 'email' => 'test@test.com', 'password' => 'abc123', 'xtra' => 'xtra'],
            2 => ['id' => 2, 'email' => 'test@yahoo.com', 'password' => 'secret'],
        ]));
    }

    public function test1(): void
    {
        $table = new Table();
        $table->setApp($this->createApp());
        $table->invokeInit();
        $table->setModel($this->m, []);

        $table->addColumn('email');
        $table->addColumn(null, new Table\Column\Template('password={$password}'));

        self::assertSame('<td>{$email}</td><td>password={$password}</td>', $table->getDataRowHtml());
        self::assertSame(
            '<tr data-id="1"><td>test@test.com</td><td>password=abc123</td></tr>',
            $this->extractTableRow($table)
        );
    }

    public function test2(): void
    {
        $table = new Table();
        $table->setApp($this->createApp());
        $table->invokeInit();
        $table->setModel($this->m, []);

        $table->addColumn('email');
        $table->addColumn('password', [Table\Column\Password::class]);

        self::assertSame('<td>{$email}</td><td>***</td>', $table->getDataRowHtml());
        self::assertSame(
            '<tr data-id="1"><td>test@test.com</td><td>***</td></tr>',
            $this->extractTableRow($table)
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
