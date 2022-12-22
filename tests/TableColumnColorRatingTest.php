<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Exception;
use Atk4\Ui\Table;

class TableColumnColorRatingTest extends TestCase
{
    use TableTestTrait;

    /** @var Table */
    public $table;

    protected function setUp(): void
    {
        parent::setUp();

        $arr = [
            'table' => [
                1 => ['id' => 1, 'name' => 'bar', 'ref' => 'ref123', 'rating' => 3],
            ],
        ];
        $db = new Persistence\Array_($arr);
        $m = new Model($db, ['table' => 'table']);
        $m->addField('name');
        $m->addField('ref');
        $m->addField('rating');
        $this->table = new Table();
        $this->table->invokeInit();
        $this->table->setModel($m, ['name', 'ref', 'rating']);
    }

    public function testValueGreaterThanMax(): void
    {
        $rating = $this->table->addDecorator('rating', [Table\Column\ColorRating::class, [
            'min' => 0,
            'max' => 2,
            'steps' => 3,
            'colors' => [
                '#FF0000',
                '#FFFF00',
                '#00FF00',
            ],
        ]]);

        static::assertSame(
            '<td>{$name}</td><td>{$ref}</td><td style="{$' . $this->getColumnStyle($rating) . '}">{$rating}</td>',
            $this->table->getDataRowHtml()
        );

        static::assertSame(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td style="background-color: #00ff00;">3</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testValueGreaterThanMaxNoColor(): void
    {
        $this->table->addDecorator('rating', [Table\Column\ColorRating::class, [
            'min' => 0,
            'max' => 2,
            'steps' => 3,
            'colors' => [
                '#FF0000',
                '#FFFF00',
                '#00FF00',
            ],
            'moreThanMaxNoColor' => true,
        ]]);

        static::assertSame(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td style="">3</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testValueLowerThanMin(): void
    {
        $rating = $this->table->addDecorator('rating', [Table\Column\ColorRating::class, [
            'min' => 4,
            'max' => 10,
            'steps' => 3,
            'colors' => [
                '#FF0000',
                '#FFFF00',
                '#00FF00',
            ],
        ]]);

        static::assertSame(
            '<td>{$name}</td><td>{$ref}</td><td style="{$' . $this->getColumnStyle($rating) . '}">{$rating}</td>',
            $this->table->getDataRowHtml()
        );

        static::assertSame(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td style="background-color: #ff0000;">3</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testValueLowerThanMinNoColor(): void
    {
        $this->table->addDecorator('rating', [Table\Column\ColorRating::class, [
            'min' => 4,
            'max' => 10,
            'steps' => 3,
            'colors' => [
                '#FF0000',
                '#FFFF00',
                '#00FF00',
            ],
            'lessThanMinNoColor' => true,
        ]]);

        static::assertSame(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td style="">3</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testExceptionMinGreaterThanMax(): void
    {
        $this->expectException(Exception::class);
        $this->table->addDecorator('rating', [Table\Column\ColorRating::class, [
            'min' => 3,
            'max' => 1,
            'steps' => 3,
            'colors' => [
                '#FF0000',
                '#FFFF00',
                '#00FF00',
            ],
        ]]);
    }

    public function testExceptionMinEqualsMax(): void
    {
        $this->expectException(Exception::class);
        $this->table->addDecorator('rating', [Table\Column\ColorRating::class, [
            'min' => 3,
            'max' => 3,
            'steps' => 3,
            'colors' => [
                '#FF0000',
                '#FFFF00',
                '#00FF00',
            ],
        ]]);
    }

    public function testExceptionZeroSteps(): void
    {
        $this->expectException(Exception::class);
        $this->table->addDecorator('rating', [Table\Column\ColorRating::class, [
            'min' => 1,
            'max' => 3,
            'steps' => 0,
            'colors' => [
                '#FF0000',
                '#FFFF00',
                '#00FF00',
            ],
        ]]);
    }

    public function testExceptionLessThan2ColorsDefined(): void
    {
        $this->expectException(Exception::class);
        $this->table->addDecorator('rating', [Table\Column\ColorRating::class, [
            'min' => 1,
            'max' => 3,
            'steps' => 3,
            'colors' => [
                '#FF0000',
            ],
        ]]);
    }
}
