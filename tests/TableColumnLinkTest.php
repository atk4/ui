<?php

namespace atk4\ui\tests;

use atk4\core\AtkPhpunit;

class TableColumnLinkTest extends AtkPhpunit\TestCase
{
    use Concerns\HandlesTable;

    public $db;
    public $table;
    public $column;

    public function setUp(): void
    {
        $arr = ['table' => [1 => ['id' => 1, 'name' => 'bar', 'ref' => 'ref123', 'salary' => -123]]];
        $db = new \atk4\data\Persistence\Array_($arr);
        $m = new \atk4\data\Model($db, 'table');
        $m->addField('name');
        $m->addField('ref');
        $m->addField('salary');
        $this->table = new \atk4\ui\Table();
        $this->table->init();
        $this->table->setModel($m, ['name', 'ref']);
    }

    public function testgetDataRowHTML()
    {
        $this->assertEquals('<td>{$name}</td><td>{$ref}</td>', $this->table->getDataRowHTML());

        $this->assertEquals(
            '<tr data-id="1"><td>bar</td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testMultipleFormatters()
    {
        $this->table->addDecorator('name', new \atk4\ui\TableColumn\Template('<b>{$name}</b>'));

        $this->assertEquals('<td><b>{$name}</b></td><td>{$ref}</td>', $this->table->getDataRowHTML());

        $this->assertEquals(
            '<tr data-id="1"><td><b>bar</b></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testTDLast()
    {
        $this->table->addColumn('salary', new \atk4\ui\TableColumn\Money());

        $this->assertEquals(
            '<td>{$name}</td><td>{$ref}</td><td class="{$_money_class} right aligned single line">{$salary}</td>',
            $this->table->getDataRowHTML()
        );

        $this->assertEquals(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td class="negative right aligned single line">-123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testTDNotLast()
    {
        $this->table->addColumn('salary', new \atk4\ui\TableColumn\Money());
        $this->table->addDecorator('salary', new \atk4\ui\TableColumn\Template('<b>{$salary}</b>'));

        $this->assertEquals(
            '<td>{$name}</td><td>{$ref}</td><td class="{$_money_class} right aligned single line"><b>{$salary}</b></td>',
            $this->table->getDataRowHTML()
        );

        $this->assertEquals(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td class="negative right aligned single line"><b>-123</b></td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testTwoMoneys()
    {
        $this->table->addDecorator('name', new \atk4\ui\TableColumn\Money());
        $this->table->addColumn('salary', new \atk4\ui\TableColumn\Money());
        $this->table->addDecorator('salary', new \atk4\ui\TableColumn\Template('<b>{$salary}</b>'));

        $this->assertEquals(
            '<td class="{$_money_class} right aligned single line">{$name}</td><td>{$ref}</td><td class="{$_money_2_class} right aligned single line"><b>{$salary}</b></td>',
            $this->table->getDataRowHTML()
        );

        $this->assertEquals(
            '<tr data-id="1"><td class=" right aligned single line">bar</td><td>ref123</td><td class="negative right aligned single line"><b>-123</b></td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testTemplateStacking()
    {
        // Simplest way to integrate
        $this->table->addDecorator('name', new \atk4\ui\TableColumn\Template('<b>{$name}</b>'));
        $this->table->addDecorator('name', new \atk4\ui\TableColumn\Template('<u>{$name}</u>'));

        $this->assertEquals(
            '<td><u><b>{$name}</b></u></td><td>{$ref}</td>',
            $this->table->getDataRowHTML()
        );

        $this->assertEquals(
            '<tr data-id="1"><td><u><b>bar</b></u></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testRender1()
    {
        // Simplest way to integrate
        $this->table->addDecorator('name', new \atk4\ui\TableColumn\Template('<b>{$name}</b>'));
        $this->table->addDecorator('name', new \atk4\ui\TableColumn\Template('<u>{$name}</u>'));

        $this->assertEquals(
            '<tr data-id="1"><td><u><b>bar</b></u></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testRender1a()
    {
        // Simplest way to integrate
        $this->table->addColumn(null, ['Template', 'hello<b>world</b>']);

        $this->assertEquals(
            '<td>{$name}</td><td>{$ref}</td><td>hello<b>world</b></td>',
            $this->table->getDataRowHTML()
        );

        $this->assertEquals(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td>hello<b>world</b></td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink1()
    {
        $this->table->addDecorator('name', new \atk4\ui\TableColumn\Link('example.php?id={$id}'));

        $this->assertEquals(
            '<td><a href="{$c_link}">{$name}</a></td><td>{$ref}</td>',
            $this->table->getDataRowHTML()
        );

        $this->assertEquals(
            '<tr data-id="1"><td><a href="example.php?id=1">bar</a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink1a()
    {
        $this->table->addDecorator('name', ['Link', 'url' => 'example.php?id={$id}']);

        $this->assertEquals(
            '<td><a href="{$c_link}">{$name}</a></td><td>{$ref}</td>',
            $this->table->getDataRowHTML()
        );

        $this->assertEquals(
            '<tr data-id="1"><td><a href="example.php?id=1">bar</a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink2()
    {
        $this->table->addDecorator('name', new \atk4\ui\TableColumn\Link(['example', 'id' => '{$id}']));

        // url is properly encoded

        $this->assertEquals(
            '<tr data-id="1"><td><a href="example.php?id=%7B%24id%7D">bar</a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink3()
    {
        $this->table->addDecorator('name', new \atk4\ui\TableColumn\Link(['example'], ['id']));

        $this->assertEquals(
            '<tr data-id="1"><td><a href="example.php?id=1">bar</a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink4()
    {
        $this->table->addDecorator('name', new \atk4\ui\TableColumn\Link(['example'], ['test' => 'id']));

        $this->assertEquals(
            '<tr data-id="1"><td><a href="example.php?test=1">bar</a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink5()
    {
        $this->table->addDecorator('name', ['Link', ['example'], ['test' => 'id']]);

        $this->assertEquals(
            '<tr data-id="1"><td><a href="example.php?test=1">bar</a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink6()
    {
        $this->table->addDecorator('name', ['Link', ['example'], ['test' => 'id'], 'force_download' => true]);

        $this->assertEquals(
            '<tr data-id="1"><td><a href="example.php?test=1" download="true" >bar</a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink7()
    {
        $this->table->addDecorator('name', ['Link', ['example'], ['test' => 'id'], 'target' => '_blank']);

        $this->assertEquals(
            '<tr data-id="1"><td><a href="example.php?test=1" target="_blank" >bar</a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink8()
    {
        $this->table->addDecorator('name', ['Link', ['example'], ['test' => 'id'], 'icon' => 'info']);

        $this->assertEquals(
            '<tr data-id="1"><td><a href="example.php?test=1"><i class="icon info"></i>bar</a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink9()
    {
        $this->table->addDecorator('name', ['Link', ['example'], ['test' => 'id'], 'use_label' => false]);

        $this->assertEquals(
            '<tr data-id="1"><td><a href="example.php?test=1"></a></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink10()
    {
        // need to reset all to set a nulled value in field name model
        $arr = ['table' => [1 => ['id' => 1, 'name' => '', 'ref' => 'ref123', 'salary' => -123]]];
        $db = new \atk4\data\Persistence\Array_($arr);
        $m = new \atk4\data\Model($db, 'table');
        $m->addField('name');
        $m->addField('ref');
        $m->addField('salary');
        $this->table = new \atk4\ui\Table();
        $this->table->init();
        $this->table->setModel($m, ['name', 'ref']);

        $this->table->addDecorator('name', ['NoValue', ['no_value' => ' --- ']]);

        $this->assertEquals(
            '<tr data-id="1"><td> --- </td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    public function testLink11()
    {
        $this->table->addDecorator('name', ['Tooltip', ['tooltip_field' => 'ref']]);

        $this->assertEquals(
            '<tr data-id="1"><td class=""> bar<span class="ui icon link " data-tooltip="ref123"><i class="ui icon info circle"></span></td><td>ref123</td></tr>',
            $this->extractTableRow($this->table)
        );
    }

    /*
    function testLink1() {

        // Simplest way to integrate
        $this->table->addColumn('name', new \atk4\ui\TableColumn\Link());

        $this->assertEquals(
            '<td><u><b>{$name}</b></u></td><td>{$ref}</td>',
            $this->table->getDataRowHTML()
        );
    }
     */
}
