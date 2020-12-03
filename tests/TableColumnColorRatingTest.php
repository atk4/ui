<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\AtkPhpunit;
use Atk4\Ui\Table;

class TableColumnColorRatingTest extends AtkPhpunit\TestCase
{
    use Concerns\HandlesTable;

    public $db;
    /** @var Table */
    public $table;
    public $column;

    protected function setUp(): void
    {
        $arr = [
            'table' => [
                1 => [
                    'id' => 1,
                    'name' => 'bar',
                    'ref' => 'ref123',
                    'rating' => 3,
                ],
            ],
        ];
        $db = new \Atk4\Data\Persistence\Array_($arr);
        $m = new \Atk4\Data\Model($db, 'table');
        $m->addField('name');
        $m->addField('ref');
        $m->addField('rating');
        $this->table = new \Atk4\Ui\Table();
        $this->table->invokeInit();
        $this->table->setModel($m, ['name', 'ref', 'rating']);
    }

    public function testValueGreaterThanMax()
    {
        $rating = $this->table->addDecorator('rating', [
            Table\Column\ColorRating::class,
            [
                'min' => 0,
                'max' => 2,
                'steps' => 3,
                'colors' => [
                    '#FF0000',
                    '#FFFF00',
                    '#00FF00',
                ],
            ],
        ]);

        $this->assertSame(
            '<td>{$name}</td><td>{$ref}</td><td style="{$' . $this->getColumnStyle($rating) . '}">{$rating}</td>',
            $this->table->getDataRowHtml()
        );

        $this->assertSame(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td style="background-color:#00ff00;">3</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testValueGreaterThanMaxNoColor()
    {
        $this->table->addDecorator('rating', [
            Table\Column\ColorRating::class,
            [
                'min' => 0,
                'max' => 2,
                'steps' => 3,
                'colors' => [
                    '#FF0000',
                    '#FFFF00',
                    '#00FF00',
                ],
                'more_than_max_no_color' => true,
            ],
        ]);

        $this->assertSame(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td style="">3</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testValueLowerThanMin()
    {
        $rating = $this->table->addDecorator('rating', [
            Table\Column\ColorRating::class,
            [
                'min' => 4,
                'max' => 10,
                'steps' => 3,
                'colors' => [
                    '#FF0000',
                    '#FFFF00',
                    '#00FF00',
                ],
            ],
        ]);

        $this->assertSame(
            '<td>{$name}</td><td>{$ref}</td><td style="{$' . $this->getColumnStyle($rating) . '}">{$rating}</td>',
            $this->table->getDataRowHtml()
        );

        $this->assertSame(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td style="background-color:#ff0000;">3</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testValueLowerThanMinNoColor()
    {
        $this->table->addDecorator('rating', [
            Table\Column\ColorRating::class,
            [
                'min' => 4,
                'max' => 10,
                'steps' => 3,
                'colors' => [
                    '#FF0000',
                    '#FFFF00',
                    '#00FF00',
                ],
                'less_than_min_no_color' => true,
            ],
        ]);

        $this->assertSame(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td style="">3</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testExceptionMinGreaterThanMax()
    {
        $this->expectException(\Atk4\Ui\Exception::class);

        $this->table->addDecorator('rating', [
            Table\Column\ColorRating::class,
            [
                'min' => 3,
                'max' => 1,
                'steps' => 3,
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
        $this->expectException(\Atk4\Ui\Exception::class);

        $this->table->addDecorator('rating', [
            Table\Column\ColorRating::class,
            [
                'min' => 3,
                'max' => 3,
                'steps' => 3,
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
        $this->expectException(\Atk4\Ui\Exception::class);

        $this->table->addDecorator('rating', [
            Table\Column\ColorRating::class,
            [
                'min' => 1,
                'max' => 3,
                'steps' => 0,
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
        $this->expectException(\Atk4\Ui\Exception::class);

        $this->table->addDecorator('rating', [
            Table\Column\ColorRating::class,
            [
                'min' => 1,
                'max' => 3,
                'steps' => 3,
                'colors' => [
                    '#FF0000',
                ],
            ],
        ]);
    }
}
