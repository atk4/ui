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

$form->onSubmit(function ($f) use ($db) {
    $str = $f->model->ref('country1')['name'] . ' ' . $f->model->ref('country2')['name'] . ' ' . (new Country($db))->tryLoad($f->model['country3'])->get('name');
    $view = new \atk4\ui\Message('Select:'); // need in behat test.
    $view->init();
    $view->text->addParagraph($str);

    return $view;
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

$modal = $app->add('Modal')->set(function ($p) {
    $a = $p->add(new \atk4\ui\FormField\AutoComplete(['placeholder' => 'Search users', 'label' => 'http://']));
    $a->setModel(new Country($p->app->db));
});
$app->add(['Button', 'Open autocomplete on a Modal window'])->on('click', $modal->show());

$app->add(['Header', 'New Lookup field']);

$form = $app->add(new \atk4\ui\Form(['segment']));
$form->add(['Label', 'Input new country information here', 'top attached'], 'AboveFields');

$c = new Country($db);
$c->addExpression('letter1', 'concat("Ends with ", substring([name], -1))');

$form->addField('country_a', [
    'Lookup',
    'model'       => new Country($db),
    'hint'        => 'Lookup field is just like AutoComplete, supports all the same options.',
    'placeholder' => 'Search for country by code, LV or UK',
    'search'      => ['name', 'iso', 'iso3'],
]);

$lookup = $form->addField('country_b', [
    'Lookup',
    'model'       => $c,
    'hint'        => 'However one or few "filtering" options can be added narrowing down the final result set',
    'placeholder' => 'Search for country by code, LV or UK',
    'search'      => ['name', 'iso', 'iso3'],
]);
$lookup->addFilter('letter1');

$form->buttonSave->set('Add Countries');
