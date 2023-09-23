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
    use CreateAppTrait;
    use TableTestTrait;

    /** @var Table */
    protected $table;

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
        $m->addField('rating', ['type' => 'integer']);
        $this->table = new Table();
        $this->table->setApp($this->createApp());
        $this->table->invokeInit();
        $this->table->setModel($m, ['name', 'ref', 'rating']);
    }

    public function testValueGreaterThanMax(): void
    {
        $rating = $this->table->addDecorator('rating', [Table\Column\ColorRating::class, [
            'min' => 0,
            'max' => 2,
            'colors' => [
                '#FF0000',
                '#FFFF00',
                '#00FF00',
            ],
        ]]);

        self::assertSame(
            '<td>{$name}</td><td>{$ref}</td><td style="{$' . $this->getColumnStyle($rating) . '}">{$rating}</td>',
            $this->table->getDataRowHtml()
        );

        self::assertSame(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td style="background-color: #00ff00;">3</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testValueGreaterThanMaxNoColor(): void
    {
        $this->table->addDecorator('rating', [Table\Column\ColorRating::class, [
            'min' => 0,
            'max' => 2,
            'colors' => [
                '#FF0000',
                '#FFFF00',
                '#00FF00',
            ],
            'moreThanMaxNoColor' => true,
        ]]);

        self::assertSame(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td style="">3</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testValueLowerThanMin(): void
    {
        $rating = $this->table->addDecorator('rating', [Table\Column\ColorRating::class, [
            'min' => 4,
            'max' => 10,
            'colors' => [
                '#FF0000',
                '#FFFF00',
                '#00FF00',
            ],
        ]]);

        self::assertSame(
            '<td>{$name}</td><td>{$ref}</td><td style="{$' . $this->getColumnStyle($rating) . '}">{$rating}</td>',
            $this->table->getDataRowHtml()
        );

        self::assertSame(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td style="background-color: #ff0000;">3</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testValueLowerThanMinNoColor(): void
    {
        $this->table->addDecorator('rating', [Table\Column\ColorRating::class, [
            'min' => 4,
            'max' => 10,
            'colors' => [
                '#FF0000',
                '#FFFF00',
                '#00FF00',
            ],
            'lessThanMinNoColor' => true,
        ]]);

        self::assertSame(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td style="">3</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testMinGreaterThanMaxException(): void
    {
        $this->expectException(Exception::class);
        $this->table->addDecorator('rating', [Table\Column\ColorRating::class, [
            'min' => 3,
            'max' => 1,
            'colors' => [
                '#FF0000',
                '#FFFF00',
                '#00FF00',
            ],
        ]]);
    }

    public function testMinEqualsMaxException(): void
    {
        $this->expectException(Exception::class);
        $this->table->addDecorator('rating', [Table\Column\ColorRating::class, [
            'min' => 3,
            'max' => 3,
            'colors' => [
                '#FF0000',
                '#FFFF00',
                '#00FF00',
            ],
        ]]);
    }

    public function testLessThan2ColorsException(): void
    {
        $this->expectException(Exception::class);
        $this->table->addDecorator('rating', [Table\Column\ColorRating::class, [
            'min' => 1,
            'max' => 3,
            'colors' => [
                '#FF0000',
            ],
        ]]);
    }
}
