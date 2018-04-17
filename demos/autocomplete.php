<?php

require 'init.php';
require 'database.php';

// create header
$app->add(['Header', 'Database-driven form with an enjoyable layout']);

$app->add(new \atk4\ui\FormField\AutoComplete(['placeholder' => 'Search users', 'label' => 'http://']))->setModel(new Country($app->db));


// create form
$form = $app->add(new \atk4\ui\Form(['segment']));
$form->add(['Label', 'Input new country information here', 'top attached'], 'AboveFields');

$m = new \atk4\data\Model($db, 'test');

// Without AutoComplete
$m->hasOne('country1', new Country());

// With AutoComplete
$m->hasOne('country2', [new Country(), 'ui' => ['form' => [
    'AutoComplete',
    'plus' => true,
]]]);

$form->setModel($m);

$form->addField('country3', [
    'AutoComplete',
    'model'       => new Country($db),
    'placeholder' => 'Search for country by code, LV or UK',
    'search'      => ['name', 'iso', 'iso3'],
]);

//$acc = $form->getField('country_id');
//$acc->actionRight = ['Button', 'Hello htere'];

$form->onSubmit(function ($f) use ($db) {
    return $f->model->ref('country1')['name'].' / '.$f->model->ref('country2')['name'].' / '.(new Country($db))->load($f->model['country3'])->get('name');
});

$app->add(['Header', 'Labels']);

// from seed
$app->add(['FormField/AutoComplete', 'placeholder' => 'Search users', 'label' => 'http://'])->setModel(new Country($app->db));

// through constructor
$app->add(new \atk4\ui\FormField\AutoComplete(['placeholder' => 'Weight', 'labelRight' => new \atk4\ui\Label(['kg', 'basic'])]));
$app->add(new \atk4\ui\FormField\AutoComplete(['label' => '$', 'labelRight' => new \atk4\ui\Label(['.00', 'basic'])]));

$app->add(new \atk4\ui\FormField\AutoComplete([
    'iconLeft'   => 'tags',
    'labelRight' => new \atk4\ui\Label(['Add Tag', 'tag']),
]));

// left/right corner is not supported, but here is work-around:
$label = new \atk4\ui\Label();
$label->addClass('left corner');
$label->add(new \atk4\ui\Icon('asterisk'));

$app->add(new \atk4\ui\FormField\AutoComplete([
    'label' => $label,
]))->addClass('left corner');

$app->add(['Header', 'Auto-complete inside modal']);

$modal = $app->add('Modal')->set(function($p) {
    $a=$p->add(new \atk4\ui\FormField\AutoComplete(['placeholder' => 'Search users', 'label' => 'http://']));
    $a->setModel(new Country($p->app->db));
});
$app->add(['Button', 'Open autocomplete on a Modal window'])->on('click', $modal->show());
