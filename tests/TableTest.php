<?php

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;

class TableTest extends AtkPhpunit\TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testAddColumnWithoutModel()
    {
        $t = new \atk4\ui\Table();
        $t->init();
        $t->setSource([
            ['one' => 1, 'two' => 2, 'three' => 3, 'four' => 4],
            ['one' => 11, 'two' => 12, 'three' => 13, 'four' => 14],
        ]);

        // 4 ways to add column
        $t->addColumn(null, new \atk4\ui\TableColumn\Link('test.php?id=1'));

        // multiple ways to add column which doesn't exist in model
        $t->addColumn('five', new \atk4\ui\TableColumn\Link('test.php?id=1'));
        $t->addColumn('six', [new \atk4\ui\TableColumn\Link('test.php?id=2')]);
        $t->addColumn('seven', [\atk4\ui\TableColumn\Link::class, ['id' => 3]]);
        $t->addColumn('eight', \atk4\ui\TableColumn\Link::class);
        $t->addColumn('nine');

        $t->render();
    }
}
