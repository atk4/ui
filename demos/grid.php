<?php

date_default_timezone_set('UTC');
include 'init.php';

$bb = $layout->add('Buttons');
$bb->add(['Button', 'Refresh Grid', 'icon'=>'refresh']);

$g = $layout->add(['Grid', 'celled'=>true]);

$bb->on('click', $g->js()->reload());

$g->setModel(new SomeData());
$g->addColumn('name', new \atk4\ui\Column\Link(['details', 'id'=>'{$id}']));
$g->addColumn('surname', new \atk4\ui\Column\Template('<td class="warning">{$surname}</td>'));
$g->addColumn('title', new \atk4\ui\Column\Status([
    'positive'=> ['Prof.'],
    'negative'=> ['Dr.'],
]));

$g->addColumn('date');
$g->addColumn('salary', new \atk4\ui\Column\Money());//->addClass('right aligned single line', 'all'));

$g->addHook('getHTMLTags', function ($grid, $row) {
    if ($row->id == 1) {
        return [
            'name'=> $grid->app->getTag('div', ['class'=>'ui ribbon label'], $row['name']),
        ];
    }
});

$g->addTotals(['name'=>'Totals:', 'salary'=>['sum']]);
