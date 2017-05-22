<?php

namespace atk4\ui\tests;

class TableColumnLinkTest extends \atk4\core\PHPUnit_AgileTestCase
{
    public $db;
    public $table;
    public $column;

    function setUp() {
        $arr = ['table'=>[1=>['id'=>1, 'name'=>'bar', 'ref'=>'ref123', 'salary'=>-123]]];
        $db = new \atk4\data\Persistence_Array($arr);
        $m = new \atk4\data\Model($db, 'table');
        $m->addField('name');
        $m->addField('ref');
        $m->addField('salary');
        $this->table = new \atk4\ui\Table();
        $this->table->setModel($m, ['name', 'ref']);
    }

    function testgetDataRowHTML() {
        $this->assertEquals('<td>{$name}</td><td>{$ref}</td>', $this->table->getDataRowHTML());

        $this->assertEquals(
            '<tr data-id="1"><td>bar</td><td>ref123</td></tr>',
            $this->extract($this->table->render())
        );
    }

    function testMultipleFormatters() {
        $this->table->addColumn('name', new \atk4\ui\TableColumn\Template('<b>{$name}</b>'));

        $this->assertEquals('<td><b>{$name}</b></td><td>{$ref}</td>', $this->table->getDataRowHTML());

        $this->assertEquals(
            '<tr data-id="1"><td><b>bar</b></td><td>ref123</td></tr>',
            $this->extract($this->table->render())
        );
    }

    function testTDLast() {
        $this->table->addColumn('salary', new \atk4\ui\TableColumn\Money());

        $this->assertEquals(
            '<td>{$name}</td><td>{$ref}</td><td class="{$_money_class} right aligned single line">{$salary}</td>', 
            $this->table->getDataRowHTML()
        );

        $this->assertEquals(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td class="negative right aligned single line">-123</td></tr>',
            $this->extract($this->table->render())
        );
    }

    function testTDNotLast() {
        $this->table->addColumn('salary', new \atk4\ui\TableColumn\Money());
        $this->table->addColumn('salary', new \atk4\ui\TableColumn\Template('<b>{$salary}</b>'));

        $this->assertEquals(
            '<td>{$name}</td><td>{$ref}</td><td class="{$_money_class} right aligned single line"><b>{$salary}</b></td>', 
            $this->table->getDataRowHTML()
        );

        $this->assertEquals(
            '<tr data-id="1"><td>bar</td><td>ref123</td><td class="negative right aligned single line"><b>-123</b></td></tr>',
            $this->extract($this->table->render())
        );
    }

    function testTwoMoneys() {
        $this->table->addColumn('name', new \atk4\ui\TableColumn\Money());
        $this->table->addColumn('salary', new \atk4\ui\TableColumn\Money());
        $this->table->addColumn('salary', new \atk4\ui\TableColumn\Template('<b>{$salary}</b>'));

        $this->assertEquals(
            '<td class="{$_money_class} right aligned single line">{$name}</td><td>{$ref}</td><td class="{$_money_2_class} right aligned single line"><b>{$salary}</b></td>',
            $this->table->getDataRowHTML()
        );

        $this->assertEquals(
            '<tr data-id="1"><td class=" right aligned single line">bar</td><td>ref123</td><td class="negative right aligned single line"><b>-123</b></td></tr>',
            $this->extract($this->table->render())
        );
    }

    function testTemplateStacking() {

        // Simplest way to integrate
        $this->table->addColumn('name', new \atk4\ui\TableColumn\Template('<b>{$name}</b>'));
        $this->table->addColumn('name', new \atk4\ui\TableColumn\Template('<u>{$name}</u>'));

        $this->assertEquals(
            '<td><u><b>{$name}</b></u></td><td>{$ref}</td>',
            $this->table->getDataRowHTML()
        );

        $this->assertEquals(
            '<tr data-id="1"><td><u><b>bar</b></u></td><td>ref123</td></tr>',
            $this->extract($this->table->render())
        );
    }

    function extract($val) {
        // extract only <tr> out
        preg_match('/<.*data-id="1".*/m',$val, $matches);
        return $matches[0];
    }

    function testRender1() {

        // Simplest way to integrate
        $this->table->addColumn('name', new \atk4\ui\TableColumn\Template('<b>{$name}</b>'));
        $this->table->addColumn('name', new \atk4\ui\TableColumn\Template('<u>{$name}</u>'));

        $this->assertEquals(
            '<tr data-id="1"><td><u><b>bar</b></u></td><td>ref123</td></tr>',
            $this->extract($this->table->render())
        );
    }

    function testLink1() {
        $this->table->addColumn('name', new \atk4\ui\TableColumn\Link('example.php?id={$id}'));

        $this->assertEquals(
            '<td><a href="{$c_link}">{$name}</a></td><td>{$ref}</td>',
            $this->table->getDataRowHTML()
        );

        $this->assertEquals(
            '<tr data-id="1"><td><a href="example.php?id=1">bar</a></td><td>ref123</td></tr>',
            $this->extract($this->table->render())
        );
    }

    function testLink1a() {
        $this->table->addColumn('name', ['TableColumn/Link', 'url'=>'example.php?id={$id}']);

        $this->assertEquals(
            '<td><a href="{$c_link}">{$name}</a></td><td>{$ref}</td>',
            $this->table->getDataRowHTML()
        );

        $this->assertEquals(
            '<tr data-id="1"><td><a href="example.php?id=1">bar</a></td><td>ref123</td></tr>',
            $this->extract($this->table->render())
        );
    }

    function testLink2() {
        $this->table->addColumn('name', new \atk4\ui\TableColumn\Link(['example', 'id'=>'{$id}']));

        // url is properly encoded

        $this->assertEquals(
            '<tr data-id="1"><td><a href="example.php?id=%7B%24id%7D">bar</a></td><td>ref123</td></tr>',
            $this->extract($this->table->render())
        );
    }

    function testLink3() {
        $this->table->addColumn('name', new \atk4\ui\TableColumn\Link(['example'], ['id']));

        $this->assertEquals(
            '<tr data-id="1"><td><a href="example.php?id=1">bar</a></td><td>ref123</td></tr>',
            $this->extract($this->table->render())
        );
    }

    function testLink4() {
        $this->table->addColumn('name', new \atk4\ui\TableColumn\Link(['example'], ['test'=>'id']));

        $this->assertEquals(
            '<tr data-id="1"><td><a href="example.php?test=1">bar</a></td><td>ref123</td></tr>',
            $this->extract($this->table->render())
        );
    }

    function testLink5() {
        $this->table->addColumn('name', ['TableColumn/Link', 'example', ['test'=>'id']]);

        $this->assertEquals(
            '<tr data-id="1"><td><a href="example.php?test=1">bar</a></td><td>ref123</td></tr>',
            $this->extract($this->table->render())
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
