<?php

require 'init.php';
require 'database.php';

// create header
$layout->add(['Header', 'Database-driven form with an enjoyable layout']);

// create form
$form = $layout->add(new \atk4\ui\Form(['segment']));
$form->add(['Label', 'Input new country information here', 'top attached'], 'AboveFields');

$form->setModel(new Country($db), false);

$form->addField('name', ['AutoComplete']);

$form->onSubmit(function ($f) {
    $notifier = new \atk4\ui\jsNotify();
    $notifier->setContent($f->model['name']);

    return $notifier;
});

