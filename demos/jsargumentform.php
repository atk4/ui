<?php

require 'init.php';
require 'database.php';

$app->add(new \atk4\ui\Header(['Enter Country model id', 'size' => 4]));
$field = $app->add(new \atk4\ui\FormField\Line(['caption' => 'Enter model id']))->set(12);

$app->add(['ui' => 'ui divider']);

$country = new Country($db);

// clicking button should simply display toast ok
$country->addAction('callback', ['callback'=> function () {
    return 'ok';
}]);

// clicking button should show preview window wiht OK. If OK is pressed should close window and display toast OK
$country->addAction('preview', ['preview'=> function () {
    return 'show this on preview screen';
}, 'callback'=>function () {
    return 'this is ok';
}]);

// clicking button no effect, because action is disabled
$country->addAction('disabled_action', ['enabled'=> false, 'callback'=>function () {
    return 'ok';
}]);

// invoking this action requires argument "age" (integer). User should be prompted, end would return age in response
$country->addAction('edit_argument', ['args'=> ['age'=>['type'=>'integer', 'required' => true]], 'callback'=>function ($m, $age) {
    return 'age = '.$age;
}]);

// user can edit 'iso3' field before action is invoked (will be blank, since it's not loaded but should show proper label). NOT SAVING! but will still show 'ok' in toast
$country->addAction('edit_iso', ['fields'=> ['iso3'], 'callback'=>function () {
    return 'ok';
}]);

// if action throws exception, need to properly display to user
$country->addAction('Ouch', ['callback'=> function () {
    throw new \atk4\ui\Exception('ouch');
}]);

// action may require confirmation, before activating
$country->addAction('confirm', ['ui' => ['confirm'=>'Call action?'], 'callback'=>function(){ return 'Confirm ok'; }]);


// action may require confirmation, before activating
$country->addAction('multi_step', ['args'=> ['age'=>['type'=>'integer', 'required'=> true], 'gender' => ['type'=> 'string', 'required'=>true]], 'fields'=> ['iso3'], 'callback'=> function ($m, $age, $gender) {
//    $m->save();
    return 'ok';
}, 'preview'=> function ($m, $age, $gender) {
    return 'Gender = '.$gender. ' / Age = '. $age;
}]);

$buttons = $app->add(['ui'=>'vertical basic buttons']);

foreach ($country->getActions() as $action) {
    $ex = $app->add(new \atk4\ui\ActionExecutor\UserAction(['stepTitle' => ['args' => ['Header', 'Fill in argument:', 'size' => 5]]]))->setAction($action);
    $ex->assignTrigger($buttons->add(['Button', $action->getDescription()]), [$ex->name => $field->jsInput()->val()]);
}