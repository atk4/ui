<?php


namespace atk4\ui\tests;

use atk4\ui\Table;

class TableColumnColorRatingTest extends \atk4\core\PHPUnit_AgileTestCase
{
    public $db;
    /** @var Table */
    public $table;
    public $column;

    protected function extract($val)
    {
        // extract only <tr> out
        preg_match('/<.*data-id="1".*/m', $val, $matches);

        return $matches[0];
    }

    public function setUp()
    {
        $arr = [
            'table' => [
                1 => [
                    'id'     => 1,
                    'name'   => 'bar',
                    'ref'    => 'ref123',
                    'rating' => 3,
                ],
            ],
        ];

        $db = new \atk4\data\Persistence\Array_($arr);
        $m = new \atk4\data\Model($db, 'table');
        $m->addField('name');
        $m->addField('ref');
        $m->addField('rating');
        $this->table = new \atk4\ui\Table();
        $this->table->init();
        $this->table->setModel($m, ['name', 'ref', 'rating']);
    }

    public function testValueGreaterThanMax()
    {
        $this->table->addDecorator('rating', [
            'ColorRating',
            [
                'min'    => 0,
                'max'    => 2,
                'steps'  => 3,
                'colors' => [
                    '#FF0000',
                    '#FFFF00',
                    '#00FF00',
                ],
            ],
        ]);

        $this->assertEquals(
            '<td>{$name}</td><td>{$ref}</td><td style="{$_colorrating_color_rating}">{$rating}</td>',
            $this->table->getDataRowHTML()
        );

        $this->assertEquals(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td style="background-color:#00ff00;">3</td></tr>',
            $this->extract($this->table->render())
        );
    }

    public function testValueGreaterThanMaxNoColor()
    {
        $this->table->addDecorator('rating', [
            'ColorRating',
            [
                'min'    => 0,
                'max'    => 2,
                'steps'  => 3,
                'colors' => [
                    '#FF0000',
                    '#FFFF00',
                    '#00FF00',
                ],
                'more_than_max_no_color' => true,
            ],
        ]);

        $this->assertEquals(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td style="">3</td></tr>',
            $this->extract($this->table->render())
        );
    }

    public function testValueLowerThanMin()
    {
        $this->table->addDecorator('rating', [
            'ColorRating',
            [
                'min'    => 4,
                'max'    => 10,
                'steps'  => 3,
                'colors' => [
                    '#FF0000',
                    '#FFFF00',
                    '#00FF00',
                ],
            ],
        ]);

        $this->assertEquals(
            '<td>{$name}</td><td>{$ref}</td><td style="{$_colorrating_color_rating}">{$rating}</td>',
            $this->table->getDataRowHTML()
        );

        $this->assertEquals(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td style="background-color:#ff0000;">3</td></tr>',
            $this->extract($this->table->render())
        );
    }

    public function testValueLowerThanMinNoColor()
    {
        $this->table->addDecorator('rating', [
            'ColorRating',
            [
                'min'    => 4,
                'max'    => 10,
                'steps'  => 3,
                'colors' => [
                    '#FF0000',
                    '#FFFF00',
                    '#00FF00',
                ],
                'less_than_min_no_color' => true,
            ],
        ]);

        $this->assertEquals(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td style="">3</td></tr>',
            $this->extract($this->table->render())
        );
    }

    public function testExceptionMinGreaterThanMax()
    {
        $this->expectException(\atk4\ui\Exception::class);

        $this->table->addDecorator('rating', [
            'ColorRating',
            [
                'min'    => 3,
                'max'    => 1,
                'steps'  => 3,
                'colors' => [
                    '#FF0000',
                    '#FFFF00',
                    '#00FF00',
                ],
            ],
        ]);
    }

    public function testExceptionMinEqualsMax()
    {
        $this->expectException(\atk4\ui\Exception::class);

        $this->table->addDecorator('rating', [
            'ColorRating',
            [
                'min'    => 3,
                'max'    => 3,
                'steps'  => 3,
                'colors' => [
                    '#FF0000',
                    '#FFFF00',
                    '#00FF00',
                ],
            ],
        ]);
    }

    public function testExceptionZeroSteps()
    {
        $this->expectException(\atk4\ui\Exception::class);

        $this->table->addDecorator('rating', [
            'ColorRating',
            [
                'min'    => 1,
                'max'    => 3,
                'steps'  => 0,
                'colors' => [
                    '#FF0000',
                    '#FFFF00',
                    '#00FF00',
                ],
            ],
        ]);
    }

    public function testExceptionLessThan2ColorsDefined()
    {
        $this->expectException(\atk4\ui\Exception::class);

        $this->table->addDecorator('rating', [
            'ColorRating',
            [
                'min'    => 1,
                'max'    => 3,
                'steps'  => 3,
                'colors' => [
                    '#FF0000',
                ],
            ],
        ]);
    }
}
