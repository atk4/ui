<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Ui\Exception;
use Atk4\Ui\Table;

class TableTest extends TestCase
{
    use CreateAppTrait;

    /**
     * @doesNotPerformAssertions
     */
    public function testAddColumnWithoutModel(): void
    {
        $t = new Table();
        $t->setApp($this->createApp());
        $t->invokeInit();
        $t->setSource([
            ['one' => 1, 'two' => 2, 'three' => 3, 'four' => 4],
            ['one' => 11, 'two' => 12, 'three' => 13, 'four' => 14],
        ]);

        // 4 ways to add column
        $t->addColumn(null, new Table\Column\Link('test.php?id=1'));

        // multiple ways to add column which doesn't exist in model
        $t->addColumn('five', new Table\Column\Link('test.php?id=1'));
        $t->addColumn('seven', [Table\Column\Link::class]);
        $t->addColumn('eight', [Table\Column\Link::class, ['id' => 3]]);
        $t->addColumn('nine');

        $t->render();
    }

    public function testAddColumnAlreadyExistsException(): void
    {
        $t = new Table();
        $t->setApp($this->createApp());
        $t->invokeInit();
        $t->addColumn('foo');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Table column already exists');
        $t->addColumn('foo');
    }
}
