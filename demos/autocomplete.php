<?php

require 'init.php';
require 'database.php';

// create header
$layout->add(['Header', 'Database-driven form with an enjoyable layout']);

// create form
$form = $layout->add(new \atk4\ui\Form(['segment']));
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

$form->onSubmit(function ($f) {
    return $f->model->ref('country_id')['name'];
});

return;

$layout->add(new \atk4\ui\FormField\AutoComplete(['placeholder' => 'Search users', 'left' => true, 'icon' => 'users']));

$layout->add(new \atk4\ui\Header(['Labels', 'size' => 2]));

$layout->add(new \atk4\ui\FormField\AutoComplete(['placeholder' => 'Search users', 'label' => 'http://']));
$layout->add(new \atk4\ui\FormField\AutoComplete(['placeholder' => 'Weight', 'labelRight' => new \atk4\ui\Label(['kg', 'basic'])]));
$layout->add(new \atk4\ui\FormField\AutoComplete(['label' => '$', 'labelRight' => new \atk4\ui\Label(['.00', 'basic'])]));

$layout->add(new \atk4\ui\FormField\AutoComplete([
    'iconLeft'   => 'tags',
    'labelRight' => new \atk4\ui\Label(['Add Tag', 'tag']),
]));

// left/right corner is not supported, but here is work-around:
$label = new \atk4\ui\Label();
$label->addClass('left corner');
$label->add(new \atk4\ui\Icon('asterisk'));

$layout->add(new \atk4\ui\FormField\AutoComplete([
    'label' => $label,
]))->addClass('left corner');
