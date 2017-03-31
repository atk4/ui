<?php

namespace atk4\ui\tests;

class TableTest extends \atk4\core\PHPUnit_AgileTestCase
{
    /**
     * Test constructor.
     */
    public function testButtonIcon()
    {
        $b = new \atk4\ui\Table();
        $b->setSource([
            ['one'=>1, 'two'=>2, 'three'=>3, 'four'=>4],
            ['one'=> 11, 'two'=>12, 'three'=>13, 'four'=>14],
        ]);

        // 4 ways to add column
        $b->addColumn(null, new \atk4\ui\TableColumn\Link('test.php?id=1'));

        $b->render();
    }
}
