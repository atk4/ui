<?php

date_default_timezone_set('UTC');
include 'init.php';

$bb = $layout->add(['View', 'ui'=>'buttons']);
$table = $layout->add(['Table', 'celled'=>true]);

$bb->add(['Button', 'Refresh Table', 'icon'=>'refresh'])
    ->on('click', new \atk4\ui\jsReload($table));


$bb->on('click', $table->js()->reload());

$table->setModel(new SomeData(), false);

$table->addColumn('name', new \atk4\ui\TableColumn\Link(['details', 'id'=>'{$id}']));
$table->addColumn('surname', new \atk4\ui\TableColumn\Template('{$surname}'))->addClass('warning');
$table->addColumn('title', new \atk4\ui\TableColumn\Status([
    'positive'=> ['Prof.'],
    'negative'=> ['Dr.'],
]));

$table->addColumn('date');
$table->addColumn('salary', new \atk4\ui\TableColumn\Money()); //->addClass('right aligned single line', 'all'));

$table->addHook('getHTMLTags', function ($table, $row) {
    if ($row->id == 1) {
        return [
            'name'=> $table->app->getTag('div', ['class'=>'ui ribbon label'], $row['name']),
        ];
    }
});

$table->addTotals(['name'=>'Totals:', 'salary'=>['sum']]);
