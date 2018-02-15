<?php

require 'init.php';
require 'database.php';

$g = $app->add(['CRUD', 'ipp'=>5]);
$g->setModel(new Country($db));

$app->add(['ui'=>'divider']);

$c = $app->add('Columns');
$cc=$c->addColumn(0, 'ui blue segment');
$cc->add(['Header', 'Configured CRUD']);
$cc->add([
    'CRUD', 
    'fieldsDefault'=>['name'], 
    'fieldsCreate'=>['iso','iso3', 'name', 'phonecode'],
    'ipp'=>5
])->setModel(new Country($app->db));

$cc=$c->addColumn();
$cc->add(['Label', 'test', 'top attached']);
