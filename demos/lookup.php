<?php

require 'init.php';
require 'database.php';

// create header
$app->add(['Header', 'Database-driven form with an enjoyable layout']);

//$app->add(new \atk4\ui\FormField\AutoComplete(['placeholder' => 'Search users', 'label' => 'http://']))->setModel(new Country($app->db));

// create form
$form = $app->add(new \atk4\ui\Form(['segment']));
$form->add(['Label', 'Input new country information here', 'top attached'], 'AboveFields');

$m = new \atk4\data\Model($db, 'test');

// Without AutoComplete
//$m->hasOne('country1', new Country());

// With AutoComplete
$m->hasOne('country2', [new Country(), 'ui' => ['form' => [
    'Lookup',
]]]);

$m->addField('allo');

$form->setModel($m);