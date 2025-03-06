<?php

declare(strict_types=1);

namespace Atk4\Ui\Tests\Table;

use Atk4\Core\Phpunit\TestCase;
use Atk4\Data\Field;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Exception;
use Atk4\Ui\Table;
use Atk4\Ui\Tests\CreateAppTrait;
use Atk4\Ui\Tests\TableTestTrait;
use Atk4\Ui\View;

class ColumnTest extends TestCase
{
    use CreateAppTrait;
    use TableTestTrait;

    protected function createTable(): Table
    {
        $arr = [
            'table' => [
                1 => ['id' => 1, 'name' => 'foo'],
                2 => ['id' => 2, 'name' => 'bar'],
            ],
        ];
        $db = new Persistence\Array_($arr);
        $m = new Model($db, ['table' => 'table']);
        $m->addField('name');

        $table = new Table();
        $table->setApp($this->createApp());
        $table->invokeInit();
        $table->setModel($m, []);

        return $table;
    }

    public function testAssertColumnViewNotInitializedException(): void
    {
        $column = new Table\Column\ActionButtons();
        $column->name = 'foo';

        $view = new View();
        $view->setApp($this->createApp());
        $view->invokeInit();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unexpected initialized View instance');
        $column->addButton($view);
    }

    public function testEachRowIsRenderIndividually(): void
    {
        $table = $this->createTable();
        $table->addColumn('name', new class extends Table\Column {
            #[\Override]
            public function getDataCellHtml(?Field $field = null, array $attr = []): string
            {
                $entity = $this->table->currentRow;

                return parent::getDataCellHtml($field, array_merge($attr, [
                    'is_foo' => $entity->get('name') === 'foo' ? 'yes' : 'no',
                ]));
            }
        });

        self::assertSame(
            [
                '<tr data-id="1"><td is_foo="yes">foo</td></tr>',
                '<tr data-id="2"><td is_foo="no">bar</td></tr>',
            ],
            [
                $this->extractTableRow($table, '1'),
                $this->extractTableRow($table, '2'),
            ]
        );
    }
}
