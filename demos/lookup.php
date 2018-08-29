<?php

require 'init.php';
require 'database.php';

// create header
$app->add(['Header', 'Database-driven form with an enjoyable layout']);

//$c = $app->add('CRUD');
//$c->setModel(new City($db));

//$app->add(new \atk4\ui\FormField\AutoComplete(['placeholder' => 'Search users', 'label' => 'http://']))->setModel(new Country($app->db));

// create form
$form = $app->add(new \atk4\ui\Form(['segment']));
$form->add(['Label', 'Add city', 'top attached'], 'AboveFields');


$l = $form->addField('city',['Lookup']);
$l->addFilter('country_test', 'Country');
$l->addFilter('language', 'Lang');


$l->setModel(new City($db));
//$m = new \atk4\data\Model($db, 'test');

// Without AutoComplete
//$m->hasOne('country1', new Country());

// With AutoComplete
//$m->hasOne('country2', [new Country(), 'ui' => ['form' => [
//    'Lookup',
//]]]);

//$m->addField('allo');

//$form->setModel($m);