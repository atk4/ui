<?php

chdir('..');
require_once 'init.php';

if ($id = $_GET['id'] ?? null) {
    $app->layout->js(true, new \atk4\ui\jsToast('Details link is in simulation mode.'));
}

$bb = \atk4\ui\View::addTo($app, ['ui' => 'buttons']);

$table = \atk4\ui\Table::addTo($app, ['celled' => true]);
\atk4\ui\Button::addTo($bb, ['Refresh Table', 'icon' => 'refresh'])
    ->on('click', new \atk4\ui\jsReload($table));

$bb->on('click', $table->js()->reload());

$table->setModel(new SomeData(), false);

$table->addColumn('name', new \atk4\ui\TableColumn\Link(['table', 'id' => '{$id}']));
$table->addColumn('surname', new \atk4\ui\TableColumn\Template('{$surname}'))->addClass('warning');
$table->addColumn('title', new \atk4\ui\TableColumn\Status([
    'positive' => ['Prof.'],
    'negative' => ['Dr.'],
]));

$table->addColumn('date');
$table->addColumn('salary', new \atk4\ui\TableColumn\Money());
$table->addColumn('logo_url', [new \atk4\ui\TableColumn\Image()], ['caption'=>'Our Logo']);

$table->onHook('getHTMLTags', function ($table, $row) {
    switch ($row->id) {
        case 1: $color = 'yellow'; break;
        case 2: $color = 'grey'; break;
        case 3: $color = 'brown'; break;
        default: $color = '';
    }
    if ($color) {
        return [
            'name' => $table->app->getTag('div', ['class' => 'ui ribbon ' . $color . ' label'], $row['name']),
        ];
    }
});

$table->addTotals(['name' => 'Totals:', 'salary' => ['sum']]);

//
$my_array = [
    ['name' => 'Vinny', 'surname' => 'Sihra', 'birthdate' => '1973-02-03', 'cv' => 'I am <strong>BIG</strong> Vinny'],
    ['name' => 'Zoe', 'surname' => 'Shatwell', 'birthdate' => '1958-08-21', 'cv' => null],
    ['name' => 'Darcy', 'surname' => 'Wild', 'birthdate' => '1968-11-01', 'cv' => 'I like <i style="color:orange">icecream</i>'],
    ['name' => 'Brett', 'surname' => 'Bird', 'birthdate' => '1988-12-20', 'cv' => null],
];

$table = \atk4\ui\Table::addTo($app);
$table->setSource($my_array, ['name']);

//$table->addColumn('name');
$table->addColumn('surname', ['Link', 'url' => 'table.php?id={$surname}']);
$table->addColumn('birthdate', null, ['type' => 'date']);
$table->addColumn('cv', ['HTML']);

$table->getColumnDecorators('name')[0]->addClass('disabled');
