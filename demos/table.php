<?php

date_default_timezone_set('UTC');
include 'init.php';

$bb = $layout->add('Buttons');
$bb->add(['Button', 'Refresh Table', 'icon'=>'refresh']);

$g = $layout->add(['Table', 'celled'=>true]);

$bb->on('click', $g->js()->reload());

$g->setModel(new SomeData(), false);

$g->addColumn('name', new \atk4\ui\TableColumn\Link(['details', 'id'=>'{$id}']));
$g->addColumn('surname', new \atk4\ui\TableColumn\Template('<td class="warning">{$surname}</td>'));
$g->addColumn('title', new \atk4\ui\TableColumn\Status([
    'positive'=> ['Prof.'],
    'negative'=> ['Dr.'],
]));

$g->addColumn('date');
$g->addColumn('salary', new \atk4\ui\TableColumn\Money()); //->addClass('right aligned single line', 'all'));

$g->addHook('getHTMLTags', function ($table, $row) {
    if ($row->id == 1) {
        return [
            'name'=> $table->app->getTag('div', ['class'=>'ui ribbon label'], $row['name']),
        ];
    }
});

$g->addTotals(['name'=>'Totals:', 'salary'=>['sum']]);
