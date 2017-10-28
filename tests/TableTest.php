<?php

namespace atk4\ui\tests;

class TableTest extends \atk4\core\PHPUnit_AgileTestCase
{
    /**
     * Test constructor.
     */
    public function testAddColumnWithoutModel()
    {
        $t = new \atk4\ui\Table();
        $t->init();
        $t->setSource([
            ['one'=>1, 'two'=>2, 'three'=>3, 'four'=>4],
            ['one'=> 11, 'two'=>12, 'three'=>13, 'four'=>14],
        ]);

        // 4 ways to add column
        $t->addColumn(null, new \atk4\ui\TableColumn\Link('test.php?id=1'));

        // multiple ways to add column which doesn't exist in model
        $t->addColumn('five', new \atk4\ui\TableColumn\Link('test.php?id=1'));
        $t->addColumn('six', [new \atk4\ui\TableColumn\Link('test.php?id=2')]);
        $t->addColumn('seven', ['Link', ['id'=>3]]);
        $t->addColumn('eight', 'Link');
        $t->addColumn('nine');

        $t->render();
    }
}
