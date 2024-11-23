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

    /** @var Table */
    protected $table;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $arr = [
            'table' => [
                1 => ['id' => 1, 'name' => 'foo'],
                2 => ['id' => 2, 'name' => 'bar'],
            ],
        ];
        $db = new Persistence\Array_($arr);
        $m = new Model($db, ['table' => 'table']);
        $m->addField('name');
        $this->table = new Table();
        $this->table->setApp($this->createApp());
        $this->table->invokeInit();
        $this->table->setModel($m, []);
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
        $this->table->addColumn('name', new class extends Table\Column {
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
                $this->extractTableRow($this->table, '1'),
                $this->extractTableRow($this->table, '2'),
            ]
        );
    }
}
