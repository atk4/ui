<?php

namespace atk4\ui\tests;

class TotalsTest extends \atk4\core\PHPUnit_AgileTestCase
{
    public $db;
    public $table;
    public $column;

    public function setUp()
    {
        $arr = ['table' => [
            1 => ['id' => 1, 'name' => 'First',  'animal' => 'Dog',  'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4],
            2 => ['id' => 2, 'name' => 'Second', 'animal' => 'Bear', 'a' => 3, 'b' => 5, 'c' => 7, 'd' => 9],
            3 => ['id' => 3, 'name' => 'Third',  'animal' => 'Cat',  'a' => 2, 'b' => 4, 'c' => 5, 'd' => 2],
        ]];

        $db = new \atk4\data\Persistence_Array($arr);
        $m = new \atk4\data\Model($db, 'table');
        $m->addField('name');
        $m->addField('animal');
        $m->addField('a');
        $m->addField('b');
        $m->addField('c');
        $m->addField('d');

        $this->table = new \atk4\ui\Table();
        $this->table->init();
        $this->table->setModel($m, ['name', 'animal', 'a', 'b', 'c', 'd']);
    }

    /**
     * Test constructor.
     */
    public function testTotals()
    {
        // add one totals plan to calculate built-in totals
        $this->table->addTotals([
            'name'  => 'Built-in totals',
            'a'     => ['sum'],
            'b'     => ['count'],
            'c'     => ['min'],
            'd'     => ['max'],
        ]);

        // add another totals row and use custom function to calculate it
        $this->table->addTotals([
            'name'  => 'Custom totals',
            'animal'=> function ($total, $value, $key, $table) {
                // longest animal name
                $name = ($total === null ? $value : $total);

                return strlen($value) > strlen($name) ? $value : $name;
            },
            'a'     => function ($total, $value, $key, $table) {
                return ($total === null ? 0 : $total) + $value * 2;
            },
        ]);

        // need to render to calculate totals
        $this->table->render();

        // assert
        $this->assertEquals($this->table->totals, [
            [
                'a'     => 6, // sum
                'b'     => 3, // count
                'c'     => 3, // min
                'd'     => 9, // max
            ],
            [
                'animal'=> 'Bear', // longest name
                'a'     => 12, // 1*2 + 3*2 + 2*2 = 12
            ],
        ]);
    }
}
