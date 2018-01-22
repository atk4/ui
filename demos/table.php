<?php

date_default_timezone_set('UTC');
include 'init.php';

$bb = $app->add(['View', 'ui' => 'buttons']);
$table = $app->add(['Table', 'celled' => true]);

$bb->add(['Button', 'Refresh Table', 'icon' => 'refresh'])
    ->on('click', new \atk4\ui\jsReload($table));

$bb->on('click', $table->js()->reload());

$table->setModel(new SomeData(), false);

$table->addColumn('name', new \atk4\ui\TableColumn\Link(['details', 'id' => '{$id}']));
$table->addColumn('surname', new \atk4\ui\TableColumn\Template('{$surname}'))->addClass('warning');
$table->addColumn('title', new \atk4\ui\TableColumn\Status([
    'positive' => ['Prof.'],
    'negative' => ['Dr.'],
]));

$table->addColumn('date');
$table->addColumn('salary', new \atk4\ui\TableColumn\Money()); //->addClass('right aligned single line', 'all'));

$table->addHook('getHTMLTags', function ($table, $row) {
    if ($row->id == 1) {
        return [
            'name' => $table->app->getTag('div', ['class' => 'ui ribbon label'], $row['name']),
        ];
    }
});

$table->addTotals(['name' => 'Totals:', 'salary' => ['sum']]);

    $my_array = [
        ['name' => 'Vinny', 'surname' => 'Sihra', 'birthdate' => new \DateTime('1973-02-03')],
        ['name' => 'Zoe', 'surname' => 'Shatwell', 'birthdate' => new \DateTime('1958-08-21')],
        ['name' => 'Darcy', 'surname' => 'Wild', 'birthdate' => new \DateTime('1968-11-01')],
        ['name' => 'Brett', 'surname' => 'Bird', 'birthdate' => new \DateTime('1988-12-20')],
    ];

    $table = $app->add('Table');
    $table->setSource($my_array, false);

    // somehow setSourec() already creates name()
    // TODO: im not sure how i feel about it
    //$table->addColumn('name');
    $table->addColumn('surname', ['Link', 'url' => 'details.php?surname={$surname}']);
    $table->addColumn('birthdate', null, ['type' => 'date']);
