<?php

require 'init.php';
require 'database.php';

// create header
$layout->add(['Header', 'Database-driven form with an enjoyable layout']);

// create form
$form = $layout->add(new \atk4\ui\Form(['segment']));
$form->add(['Label', 'Input new country information here', 'top attached'], 'AboveFields');

$form->setModel(new Country($db), false);
//$form->addField('test');
$form->addField('name', ['AutoComplete']);

$form->onSubmit(function ($f) {
    $notifier = new \atk4\ui\jsNotify();
    $notifier->setContent($f->model['name']);

    return $notifier;
});

$layout->add(new \atk4\ui\FormField\AutoComplete(['placeholder'=>'Search users', 'left'=>true, 'icon'=>'users']));

$layout->add(new \atk4\ui\Header(['Labels', 'size'=>2]));

$layout->add(new \atk4\ui\FormField\AutoComplete(['placeholder'=>'Search users', 'label'=>'http://']));
$layout->add(new \atk4\ui\FormField\AutoComplete(['placeholder'=>'Weight', 'labelRight'=>new \atk4\ui\Label(['kg', 'basic'])]));
$layout->add(new \atk4\ui\FormField\AutoComplete(['label'=>'$', 'labelRight'=>new \atk4\ui\Label(['.00', 'basic'])]));

$layout->add(new \atk4\ui\FormField\AutoComplete([
    'iconLeft'  => 'tags',
    'labelRight'=> new \atk4\ui\Label(['Add Tag', 'tag']),
]));

// left/right corner is not supported, but here is work-around:
$label = new \atk4\ui\Label();
$label->addClass('left corner');
$label->add(new \atk4\ui\Icon('asterisk'));

$layout->add(new \atk4\ui\FormField\AutoComplete([
    'label'=> $label,
]))->addClass('left corner');