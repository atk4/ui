<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Js\JsReload;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\Table;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

if ($app->tryGetRequestQueryParam('id')) {
    $app->layout->js(true, new JsToast('Details link is in simulation mode.'));
}

$bb = View::addTo($app, ['ui' => 'buttons']);

$table = Table::addTo($app, ['class.celled' => true]);
Button::addTo($bb, ['Refresh Table', 'icon' => 'refresh'])
    ->on('click', new JsReload($table));

$table->setModel(new SomeData(), []);

$table->addColumn('name', new Table\Column\Link(['table', 'foo' => 'bar'], ['person_id' => 'id'], ['target' => '_blank']));
$table->addColumn('surname', new Table\Column\Template('{$surname}'))->addClass('warning');
$table->addColumn('title', new Table\Column\Status([
    'positive' => ['Prof.'],
    'negative' => ['Dr.'],
]));

$table->addColumn('date');
$table->addColumn('salary', new Table\Column\Money());
$table->addColumn('logo_url', [Table\Column\Image::class, 'caption' => 'Our Logo']);

$table->onHook(Table\Column::HOOK_GET_HTML_TAGS, static function (Table $table, Model $row) {
    switch ($row->getId()) {
        case 1:
            $color = 'yellow';

            break;
        case 2:
            $color = 'grey';

            break;
        case 3:
            $color = 'brown';

            break;
        default:
            $color = '';
    }
    if ($color) {
        return [
            'name' => $table->getApp()->getTag('div', ['class' => 'ui ribbon ' . $color . ' label'], $row->get('name')),
        ];
    }
});

$table->addTotals([
    'name' => 'Totals:',
    'salary' => ['sum'],
]);

$myArray = [
    ['name' => 'Vinny', 'surname' => 'Sihra', 'birthdate' => '1973-02-03', 'cv' => 'I am <strong>BIG</strong> Vinny'],
    ['name' => 'Zoe', 'surname' => 'Shatwell', 'birthdate' => '1958-08-21', 'cv' => null],
    ['name' => 'Darcy', 'surname' => 'Wild', 'birthdate' => '1968-11-01', 'cv' => 'I like <i style="color: orange;>icecream</i>'],
    ['name' => 'Brett', 'surname' => 'Bird', 'birthdate' => '1988-12-20', 'cv' => null],
];

$table = Table::addTo($app);
$table->setSource($myArray, ['name']);

// $table->addColumn('name');
$table->addColumn('surname', [Table\Column\Link::class, 'url' => 'table.php?id={$surname}']);
$table->addColumn('birthdate', [], ['type' => 'date']);
$table->addColumn('cv', [Table\Column\Html::class]);

$table->getColumnDecorators('name')[0]->addClass('disabled');
