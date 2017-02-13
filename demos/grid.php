<?php

date_default_timezone_set('UTC');
include 'init.php';

$g = $layout->add('Grid');
$g->setModel(new SomeData());
$g->addColumn('name', new \atk4\ui\Column\Link(['details', 'id'=>'{$id}']));
$g->addColumn('surname', new \atk4\ui\Column\Template('<td class="warning">{$surname}</td>'));
$g->addColumn('title', new \atk4\ui\Column\Status([
    'positive'=> ['Prof.'],
    'negative'=> ['Dr.'],
]));
