<?php

require 'init.php';
require 'database.php';

$c = $app->add('Columns');

$grid = $c->addColumn()->add(['Grid', 'ipp'=>10, 'menu'=>false]);
$grid->setModel(new Country($db), ['name']);

$country_loader = $c->addColumn()->add(['Loader', 'loadEvent'=>false, 'shim'=>['Text', 'Select country on your left']]);

$grid->table->onRowClick($country_loader->jsLoad(['id'=>$grid->table->jsRow()->data('id')]));

$country_loader->set(function($p) use($db) {
    $p->add('Form')->setModel(new Country($db))->load($_GET['id']);
});

