<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests\Table\Column;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Exception;
use Atk4\Ui\Table;
use Atk4\Ui\Tests\CreateAppTrait;
use Atk4\Ui\Tests\TableTestTrait;

class ColorRatingTest extends TestCase
{
    use CreateAppTrait;
    use TableTestTrait;

    protected function createTable(): Table
    {
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

        $table = new Table();
        $table->setApp($this->createApp());
        $table->invokeInit();
        $table->setModel($m, ['name', 'ref', 'rating']);

        return $table;
    }

    public function testValueGreaterThanMax(): void
    {
        $table = $this->createTable();
        $rating = $table->addDecorator('rating', [Table\Column\ColorRating::class, [
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
            $table->getDataRowHtml()
        );

        self::assertSame(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td style="background-color: #00ff00;">3</td></tr>',
            $this->extractTableRow($table)
        );
    }

    public function testValueGreaterThanMaxNoColor(): void
    {
        $table = $this->createTable();
        $table->addDecorator('rating', [Table\Column\ColorRating::class, [
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
            $this->extractTableRow($table)
        );
    }

    public function testValueLowerThanMin(): void
    {
        $table = $this->createTable();
        $rating = $table->addDecorator('rating', [Table\Column\ColorRating::class, [
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
            $table->getDataRowHtml()
        );

        self::assertSame(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td style="background-color: #ff0000;">3</td></tr>',
            $this->extractTableRow($table)
        );
    }

    public function testValueLowerThanMinNoColor(): void
    {
        $table = $this->createTable();
        $table->addDecorator('rating', [Table\Column\ColorRating::class, [
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
            $this->extractTableRow($table)
        );
    }

    public function testMinGreaterThanMaxException(): void
    {
        $table = $this->createTable();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Min must be lower than Max');
        $table->addDecorator('rating', [Table\Column\ColorRating::class, [
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
        $table = $this->createTable();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Min must be lower than Max');
        $table->addDecorator('rating', [Table\Column\ColorRating::class, [
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
        $table = $this->createTable();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('At least 2 colors must be set');
        $table->addDecorator('rating', [Table\Column\ColorRating::class, [
            'min' => 1,
            'max' => 3,
            'colors' => [
                '#FF0000',
            ],
        ]]);
    }
}
