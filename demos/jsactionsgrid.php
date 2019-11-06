<?php

require 'init.php';
require 'database.php';

$country = new Country($db);

$country->addAction('callback', ['callback'=> function ($m) {
    return 'ok '.$m->getTitle();
}]);

$country->addAction('preview', ['preview'=> function ($m) {
    return 'Previewing country '.$m->getTitle();
}, 'callback'=>function ($m) {
    return 'Done previewing '.$m->getTitle();
}]);

$country->addAction('disabled_action', ['enabled'=> false, 'callback'=>function () {
    return 'ok';
}]);

$country->addAction('edit_argument', ['args'=> ['age'=>['type'=>'integer', 'required' => true]], 'callback'=>function ($m, $age) {
    return 'Proper age to visit '.$m->getTitle().' is '.$age;
}]);

$country->addAction('edit_argument_prev', ['args'=> ['age'=>['type'=>'integer', 'required' => true]], 'preview'=> function ($m, $age) {
    return 'You age is: '.$age;
}, 'callback'=>function ($m, $age) {
    return 'age = '.$age;
}]);

$country->addAction('edit_iso', ['fields'=> ['iso3'], 'callback'=>function () {
    return 'ok';
}]);

$country->addAction('Ouch', ['args'=> ['age'=>['type'=>'integer']], 'preview'=> function () {
    return 'Be careful with this action.';
}, 'callback'=> function () {
    throw new \atk4\ui\Exception('Told you, didn\'t I?');
}]);
$country->addAction('confirm', ['ui' => ['confirm'=>'Call action?'], 'callback'=>function ($m) {
    return 'Confirm ok '.$m->getTitle();
}]);

$country->addAction('multi_step',
    [
        'args'  => [
            'age'    => ['type'=>'integer', 'required'=> true],
            'gender' => ['type'=> 'enum', 'values' => ['Male' => 'Male', 'Female' => 'Female'], 'required'=>true],
        ],
        'fields' => ['iso3'],
        'preview'=> function ($m, $age, $gender) {
            return 'Gender = '.$gender.' / Age = '.$age;
        },
        'callback'=> function ($m, $age, $gender) {
            return 'You are a '.$gender.' of age '.$age.' who want to visit '.$m->getTitle();
        },
    ]
);

$g = $app->add(['Grid']);
$g->setModel($country);

$g->addActionMenuItem('callback');
$g->addActionMenuItem('preview');
$g->addActionMenuItem('disabled_action');
$g->addActionMenuItem('edit_argument');
$g->addActionMenuItem('edit_argument_prev');
$g->addActionMenuItem('edit_iso');
$g->addActionMenuItem('Ouch');
$g->addActionMenuItem('confirm');
$g->addActionMenuItem('multi_step');

$g->ipp = 100;
