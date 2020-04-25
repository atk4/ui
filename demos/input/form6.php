<?php

chdir('..');
require_once 'atk-init.php';

\atk4\ui\View::addTo($app, [
    'Forms below demonstrate how to work with multi-value selectors',
    'ui' => 'ignored warning message',
]);

$cc = \atk4\ui\Columns::addTo($app);
$f = \atk4\ui\Form::addTo($cc->addColumn());

$f->addField('one', null, ['enum'=>['female', 'male']])->set('male');
$f->addField('two', ['Radio'], ['enum'=>['female', 'male']])->set('male');

$f->addField('three', null, ['values'=>['female', 'male']])->set(1);
$f->addField('four', ['Radio'], ['values'=>['female', 'male']])->set(1);

$f->addField('five', null, ['values'=>[5=>'female', 7=>'male']])->set(7);
$f->addField('six', ['Radio'], ['values'=>[5=>'female', 7=>'male']])->set(7);

$f->addField('seven', null, ['values'=>['F'=>'female', 'M'=>'male']])->set('M');
$f->addField('eight', ['Radio'], ['values'=>['F'=>'female', 'M'=>'male']])->set('M');

$f->onSubmit(function ($f) {
    echo json_encode($f->model->get());
});
