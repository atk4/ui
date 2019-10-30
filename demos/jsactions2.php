<?php

require 'init.php';
require 'database.php';

$country = new Country($db);
$id = $country->tryLoadAny()->get('id');
$country->unload();

$buttons = $app->add(['ui'=>'vertical basic buttons']);

/*
// For demonstration purpose with add, edit and delete action.

$ac = $country->getAction('add');
$add_button = $buttons->add(['Button', $ac->getDescription()]);
$add_button->on('click', $ac);

$ac = $country->getAction('edit');
$btn = $buttons->add(['Button', $ac->getDescription()]);
$btn->on('click', $ac, ['args' => ['id' => $field->jsInput()->val()]]);

$ac = $country->getAction('delete');
$btn = $buttons->add(['Button', $ac->getDescription()]);
$btn->on('click', $ac, ['confirm' => 'This will delete record. Sure?']);
*/

// clicking button should simply display toast ok with model title
$ac = $country->addAction('callback', ['callback'=> function ($m) {
    return 'ok '.$m->getTitle();
}]);
$btn = $buttons->add(['Button', $ac->getDescription()]);
$btn->on('click', $ac, ['args' => ['id' => $id]]);

// clicking button should show preview window wiht OK. If OK is pressed should close window and display toast OK
$ac = $country->addAction('preview', ['preview'=> function ($m) {
    return 'Previewing country '.$m->getTitle();
}, 'callback'=>function ($m) {
    return 'Done previewing '.$m->getTitle();
}]);
$btn = $buttons->add(['Button', $ac->getDescription()]);
$btn->on('click', $ac, ['args' => ['id' => $id]]);

// clicking button no effect, because action is disabled
$ac = $country->addAction('disabled_action', ['enabled'=> false, 'callback'=>function () {
    return 'ok';
}]);
$btn = $buttons->add(['Button', $ac->getDescription()]);
$btn->on('click', $ac, ['args' => ['id' => $id]]);

// invoking this action requires argument "age" (integer). User should be prompted, end would return age in response
$ac = $country->addAction('edit_argument', ['args'=> ['age'=>['type'=>'integer', 'required' => true]], 'callback'=>function ($m, $age) {
    return 'Proper age to visit '.$m->getTitle().' is '.$age;
}]);
$btn = $buttons->add(['Button', $ac->getDescription()]);
$btn->on('click', $ac, ['args' => ['id' => $id]]);

// invoking this action requires argument "age" (integer). User should be prompted, end would return age in response
$ac = $country->addAction('edit_argument_prev', ['args'=> ['age'=>['type'=>'integer', 'required' => true]], 'preview'=> function ($m, $age) {
    return 'You age is: '.$age;
}, 'callback'=>function ($m, $age) {
    return 'age = '.$age;
}]);
$btn = $buttons->add(['Button', $ac->getDescription()]);
$btn->on('click', $ac, ['args' => ['id' => $id]]);

// user can edit 'iso3' field before action is invoked (will be blank, since it's not loaded but should show proper label). NOT SAVING! but will still show 'ok' in toast
$ac = $country->addAction('edit_iso', ['fields'=> ['iso3'], 'callback'=>function () {
    return 'ok';
}]);
$btn = $buttons->add(['Button', $ac->getDescription()]);
$btn->on('click', $ac, ['args' => ['id' => $id]]);

// if action throws exception, need to properly display to user
$ac = $country->addAction('Ouch', ['args'=> ['age'=>['type'=>'integer']], 'preview'=> function () {
    return 'Be careful with this action.';
}, 'callback'=> function () {
    throw new \atk4\ui\Exception('Told you, didn\'t I?');
}]);
$btn = $buttons->add(['Button', $ac->getDescription()]);
$btn->on('click', $ac, ['args' => ['id' => $id]]);

// action may require confirmation, before activating
$ac = $country->addAction('confirm', ['ui' => ['confirm'=>'Call action?'], 'callback'=>function () {
    return 'Confirm ok';
}]);
$btn = $buttons->add(['Button', $ac->getDescription()]);
$btn->on('click', $ac, ['args' => ['id' => $id], 'confirm' => $ac->ui['confirm']]);

// action may require confirmation, before activating
$ac = $country->addAction('multi_step', ['args'=> ['age'=>['type'=>'integer', 'required'=> true], 'gender' => ['type'=> 'enum', 'values' => ['m' => 'Male', 'f' => 'Female'], 'required'=>true]], 'fields'=> ['iso3'], 'callback'=> function ($m, $age, $gender) {
//    $m->save();
    return 'ok';
}, 'preview'=> function ($m, $age, $gender) {
    return 'Gender = '.$gender.' / Age = '.$age;
}]);
$btn = $buttons->add(['Button', $ac->getDescription()]);
$btn->on('click', $ac, ['args' => ['id' =>$id]]);
