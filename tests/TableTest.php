<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\AtkPhpunit;

class TableTest extends AtkPhpunit\TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testAddColumnWithoutModel()
    {
        $t = new \Atk4\Ui\Table();
        $t->invokeInit();
        $t->setSource([
            ['one' => 1, 'two' => 2, 'three' => 3, 'four' => 4],
            ['one' => 11, 'two' => 12, 'three' => 13, 'four' => 14],
        ]);

        // 4 ways to add column
        $t->addColumn(null, new \Atk4\Ui\Table\Column\Link('test.php?id=1'));

        // multiple ways to add column which doesn't exist in model
        $t->addColumn('five', new \Atk4\Ui\Table\Column\Link('test.php?id=1'));
        $t->addColumn('seven', [\Atk4\Ui\Table\Column\Link::class, ['id' => 3]]);
        $t->addColumn('eight', \Atk4\Ui\Table\Column\Link::class);
        $t->addColumn('nine');

        $t->render();
    }
}
