<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Table;

class TableColumnLinkTest extends TestCase
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
                1 => ['id' => 1, 'name' => 'bar', 'ref' => 'ref123', 'salary' => -123],
            ],
        ];
        $db = new Persistence\Array_($arr);
        $m = new Model($db, ['table' => 'table']);
        $m->addField('name');
        $m->addField('ref');
        $m->addField('salary');
        $this->table = new Table();
        $this->table->setApp($this->createApp());
        $this->table->invokeInit();
        $this->table->setModel($m, ['name', 'ref']);
    }

    public function testgetDataRowHtml(): void
    {
        self::assertSame('<td>{$name}</td><td>{$ref}</td>', $this->table->getDataRowHtml());

        self::assertSame(
            '<tr data-id="1"><td>bar</td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testMultipleFormatters(): void
    {
        $this->table->addDecorator('name', new Table\Column\Template('<b>{$name}</b>'));

        self::assertSame('<td><b>{$name}</b></td><td>{$ref}</td>', $this->table->getDataRowHtml());

        self::assertSame(
            '<tr data-id="1"><td><b>bar</b></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testTdLast(): void
    {
        $salary = $this->table->addColumn('salary', new Table\Column\Money());

        self::assertSame(
            '<td>{$name}</td><td>{$ref}</td><td class="{$' . $this->getColumnClass($salary) . '} right aligned single line">{$salary}</td>',
            $this->table->getDataRowHtml()
        );

        self::assertSame(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td class="negative right aligned single line">-123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testTdNotLast(): void
    {
        $salary = $this->table->addColumn('salary', new Table\Column\Money());
        $this->table->addDecorator('salary', new Table\Column\Template('<b>{$salary}</b>'));

        self::assertSame(
            '<td>{$name}</td><td>{$ref}</td><td class="{$' . $this->getColumnClass($salary) . '} right aligned single line"><b>{$salary}</b></td>',
            $this->table->getDataRowHtml()
        );

        self::assertSame(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td class="negative right aligned single line"><b>-123</b></td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testTwoMoneys(): void
    {
        $salary1 = $this->table->addDecorator('name', new Table\Column\Money());
        $salary2 = $this->table->addColumn('salary', new Table\Column\Money());
        $this->table->addDecorator('salary', new Table\Column\Template('<b>{$salary}</b>'));

        self::assertSame(
            '<td class="{$' . $this->getColumnClass($salary1) . '} right aligned single line">{$name}</td><td>{$ref}</td><td class="{$' . $this->getColumnClass($salary2) . '} right aligned single line"><b>{$salary}</b></td>',
            $this->table->getDataRowHtml()
        );

        self::assertSame(
            '<tr data-id="1"><td class=" right aligned single line">bar</td><td>ref123</td><td class="negative right aligned single line"><b>-123</b></td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testTemplateStacking(): void
    {
        $this->table->addDecorator('name', new Table\Column\Template('<b>{$name}</b>'));
        $this->table->addDecorator('name', new Table\Column\Template('<u>{$name}</u>'));

        self::assertSame(
            '<td><u><b>{$name}</b></u></td><td>{$ref}</td>',
            $this->table->getDataRowHtml()
        );

        self::assertSame(
            '<tr data-id="1"><td><u><b>bar</b></u></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testRender1(): void
    {
        $this->table->addDecorator('name', new Table\Column\Template('<b>{$name}</b>'));
        $this->table->addDecorator('name', new Table\Column\Template('<u>{$name}</u>'));

        self::assertSame(
            '<tr data-id="1"><td><u><b>bar</b></u></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testRender1a(): void
    {
        $this->table->addColumn(null, [Table\Column\Template::class, 'hello<b>world</b>']);

        self::assertSame(
            '<td>{$name}</td><td>{$ref}</td><td>hello<b>world</b></td>',
            $this->table->getDataRowHtml()
        );

        self::assertSame(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td>hello<b>world</b></td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink1(): void
    {
        $link = $this->table->addDecorator('name', new Table\Column\Link('example.php?id={$id}'));

        self::assertSame(
            '<td><a href="{$' . $this->getColumnRef($link) . '}">{$name}</a></td><td>{$ref}</td>',
            $this->table->getDataRowHtml()
        );

        self::assertSame(
            '<tr data-id="1"><td><a href="example.php?id=1">bar</a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink1a(): void
    {
        $link = $this->table->addDecorator('name', [Table\Column\Link::class, 'url' => 'example.php?id={$id}']);

        self::assertSame(
            '<td><a href="{$' . $this->getColumnRef($link) . '}">{$name}</a></td><td>{$ref}</td>',
            $this->table->getDataRowHtml()
        );

        self::assertSame(
            '<tr data-id="1"><td><a href="example.php?id=1">bar</a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink2(): void
    {
        $this->table->addDecorator('name', new Table\Column\Link(['example', 'id' => '{$id}']));

        // url is properly encoded

        self::assertSame(
            '<tr data-id="1"><td><a href="example.php?id=%7B%24id%7D">bar</a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink3(): void
    {
        $this->table->addDecorator('name', new Table\Column\Link(['example'], ['id']));

        self::assertSame(
            '<tr data-id="1"><td><a href="example.php?id=1">bar</a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink4(): void
    {
        $this->table->addDecorator('name', new Table\Column\Link(['example'], ['test' => 'id']));

        self::assertSame(
            '<tr data-id="1"><td><a href="example.php?test=1">bar</a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink5(): void
    {
        $this->table->addDecorator('name', [Table\Column\Link::class, ['example'], ['test' => 'id']]);

        self::assertSame(
            '<tr data-id="1"><td><a href="example.php?test=1">bar</a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink6(): void
    {
        $this->table->addDecorator('name', [Table\Column\Link::class, ['example'], ['test' => 'id'], 'forceDownload' => true]);

        self::assertSame(
            '<tr data-id="1"><td><a href="example.php?test=1" download="true">bar</a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink7(): void
    {
        $this->table->addDecorator('name', [Table\Column\Link::class, ['example'], ['test' => 'id'], 'target' => '_blank']);

        self::assertSame(
            '<tr data-id="1"><td><a href="example.php?test=1" target="_blank">bar</a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink8(): void
    {
        $this->table->addDecorator('name', [Table\Column\Link::class, ['example'], ['test' => 'id'], 'icon' => 'info']);

        self::assertSame(
            '<tr data-id="1"><td><a href="example.php?test=1"><i class="info icon"></i>bar</a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink9(): void
    {
        $this->table->addDecorator('name', [Table\Column\Link::class, ['example'], ['test' => 'id'], 'useLabel' => false]);

        self::assertSame(
            '<tr data-id="1"><td><a href="example.php?test=1"></a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink10(): void
    {
        $this->table->model->load(1)->save(['name' => '']);

        $this->table->addDecorator('name', [Table\Column\NoValue::class, ['noValue' => ' --- ']]);

        self::assertSame(
            '<tr data-id="1"><td> --- </td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink11(): void
    {
        $this->table->addDecorator('name', [Table\Column\Tooltip::class, ['tooltipField' => 'ref']]);

        self::assertSame(
            '<tr data-id="1"><td class=""> bar<span class="ui icon link " data-tooltip="ref123"><i class="ui icon info circle"></span></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }
}
