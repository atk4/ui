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
            4 => ['id'=>4, 'name'=>'Bicycle', 'type'=>'transport', 'price'=>50,  'cnt'=>2, 'amount'=>100, 'balance'=>327],
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
    public function testBuiltinRowTotals()
    {
        // add one totals plan to calculate built-in row totals
        $this->table->addTotals([
            'name'   => 'Totals:', // Totals:
            'type'   => ['count'], // 4
            'price'  => ['min'],   // 1
            'cnt'    => ['max'],   // 5
            'amount' => ['sum'],   // 327
        ]);

        // need to render to calculate row totals
        $this->table->render();

        // assert
        $this->assertEquals([
                'type'      => 4,
                'price'     => 1,
                'cnt'       => 5,
                'amount'    => 327,
                '_row_count'=> 4,
            ], $this->table->totals[0]
        );
    }

    /**
     * Test advanced totals methods.
     */
    public function testAdvancedRowTotals()
    {
        // add first totals plan
        $this->table->addTotals([
            'name'   => 'Total {$_row_count} rows', // Total 4 rows
            'type'   => function ($totals, $model) {
                return 'Pay me: '.$totals['price'] * $totals['cnt'];
            }, // 25600
            'price'  => [
                            function ($total, $value, $model) {
                                return $total + $value;
                            },
                        ], // 256 - simple sum(price)
            'cnt'    => [
                            function ($total, $value, $model) {
                                return max($total, $value);
                            },
                            'default' => 100,
                        ], // 100 - uses default value max(100, max(cnt))
            'amount' => [
                            'sum',
                            'default' => function ($value, $model) {
                                return $value * 1000;
                            },
                        ], // 2327 = 2*1000 + sum(amount)
        ], 'first');

        // need to render to calculate row totals
        $this->table->render();

        // assert
        $this->assertEquals([
                //'type'    => 25600,
                'price'     => 256,
                'cnt'       => 100,
                'amount'    => 2327,
                '_row_count'=> 4,
            ], $this->table->totals['first']
        );
    }
}
