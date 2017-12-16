<?php

require 'init.php';

$app->add(new \atk4\ui\View([
    'Forms below demonstrate how to work with multi-value selectors',
    'ui' => 'ignored warning message',
]));

$cc = $app->add('Columns');
$f = $cc->addColumn()->add(new \atk4\ui\Form());

$f->addField('one', null, ['enum'=>['female', 'male']])->set('male');
$f->addField('two', ['Radio'], ['enum'=>['female', 'male']])->set('male');

$f->addField('three', null, ['values'=>['female', 'male']])->set(1);
$f->addField('four', ['Radio'], ['values'=>['female', 'male']])->set(1);
$f->onSubmit(function ($f) {
    echo json_encode($f->model->get());
});
