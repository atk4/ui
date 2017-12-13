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
            1 => ['id'=>1, 'name'=>'Sock',    'type'=>'clothes',   'price'=>1,   'cnt'=>2, 'amount'=>2,   'balance'=>2],
            2 => ['id'=>2, 'name'=>'Hat',     'type'=>'clothes',   'price'=>5,   'cnt'=>5, 'amount'=>25,  'balance'=>27],
            3 => ['id'=>3, 'name'=>'Car',     'type'=>'transport', 'price'=>200, 'cnt'=>1, 'amount'=>200, 'balance'=>227],
            3 => ['id'=>3, 'name'=>'Bicycle', 'type'=>'transport', 'price'=>50,  'cnt'=>2, 'amount'=>100, 'balance'=>327],
        ]];

        $db = new \atk4\data\Persistence_Array($arr);
        $m = new \atk4\data\Model($db, 'table');
        $m->addField('name');
        $m->addField('type');
        $m->addField('price');
        $m->addField('cnt');
        $m->addField('amount');
        $m->addField('balance');

        $this->table = new \atk4\ui\Table();
        $this->table->init();
        $this->table->setModel($m, ['name', 'type', 'price', 'cnt', 'amount', 'balance']);
    }

    /**
     * Test built-in totals methods.
     */
    public function testBuiltinTotals()
    {
        // add one totals plan to calculate built-in totals
        $this->table->addTotals([
            'name'   => 'Totals:',
            'type'   => ['count'], // 4
            'price'  => ['min'],   // 1
            'cnt'    => ['max'],   // 5
            'amount' => ['sum'],   // 327
        ]);

        // need to render to calculate totals
        $this->table->render();

        // assert
        $this->assertEquals([
                'type'   => 4,
                'price'  => 1,
                'cnt'    => 5,
                'amount' => 327,
            ], $this->table->totals[0]
        );
    }

    /*
        // add another totals row and use custom function to calculate it
        $this->table->addTotals([
            'name'  => 'Custom totals',
            'animal'=> function ($total, $value, $model, $table) {
                // longest animal name
                $name = ($total === null ? $value : $total);

                return strlen($value) > strlen($name) ? $value : $name;
            },
            'a'     => function ($total, $value, $model, $table) {
                return ($total === null ? 0 : $total) + $value * 2 + $model['b'];
            },
        ]);

            [
                'animal'=> 'Bear', // longest name
                'a'     => 23, // (1*2+2) + (3*2+5) + (2*2+4) = 23
            ],
        ]);
    }
    */

    /*
     * Test final totals.
     */
    /*
    public function testFinalTotals()
    {
        // add one totals plan to calculate built-in totals
        $this->table->addTotals(
            // executed for each row
            [
                'name'  => 'Built-in totals',
                'a'     => ['min'],
                'b'     => ['max'],
            ],
            // executed at the end
            [
                'c'     => 'Average',
                'd'     => function ($totals, $table) {
                    return round(($totals['a'] + $totals['b'])/2);
                },
            ]);


        // need to render to calculate totals
        $this->table->render();

        // assert
        $this->assertEquals($this->table->totals, [
            [
                'a'     => 1, // min
                'b'     => 5, // max
                'd'     => 3, // round((min+max)/2)
            ],
        ]);

    }
    */
}
