<?php

require 'init.php';
require 'database.php';
use atk4\ui\ActionExecutor\jsEvent;
use atk4\ui\ActionExecutor\UserAction;

$country = new Country($db);
$id = $country->tryLoadAny()->get('id');
$country->unload();

$app->add(new \atk4\ui\Header(['Enter Country model id', 'size' => 4]));
$field = $app->add(new \atk4\ui\FormField\Line(['caption' => 'Enter model id']))->set($id);

$app->add(['ui' => 'ui divider']);
$buttons = $app->add(['ui'=>'vertical basic buttons']);

$ex = $app->add(new UserAction())->setAction($ac = $country->getAction('add'));
$ex->assignTrigger($buttons->add(['Button', $ac->getDescription()]), [$ex->name => $field->jsInput()->val()]);

$ex = $app->add(new UserAction())->setAction($ac = $country->getAction('edit'));
$ex->assignTrigger($buttons->add(['Button', $ac->getDescription()]), [$ex->name => $field->jsInput()->val()]);

$ac = $country->getAction('delete');
$ex = new jsEvent($btn = $buttons->add(['Button', $ac->getDescription()]), $ac, $field->jsInput()->val());
$btn->on('click', $ex, ['confirm' => 'This will delete record. Sure?']);

// clicking button should simply display toast ok
$ac = $country->addAction('callback', ['callback'=> function () {
    return 'ok';
}]);
$ex = $app->add(new UserAction())->setAction($ac);
$ex = new jsEvent($btn = $buttons->add(['Button', $ac->getDescription()]), $ac, $field->jsInput()->val());
$btn->on('click', $ex);

// clicking button should show preview window wiht OK. If OK is pressed should close window and display toast OK
$ac = $country->addAction('preview', ['preview'=> function () {
    return 'show this on preview screen';
}, 'callback'=>function () {
    return 'this is ok';
}]);
$ex = $app->add(new UserAction(['stepTitle' => ['preview' => ['Header', 'Previewing action prior to execute:', 'size' => 5]]]))->setAction($ac);
$ex->assignTrigger($buttons->add(['Button', $ac->getDescription()]), [$ex->name => $field->jsInput()->val()]);

// clicking button no effect, because action is disabled
$ac = $country->addAction('disabled_action', ['enabled'=> false, 'callback'=>function () {
    return 'ok';
}]);
$ex = new jsEvent($btn = $buttons->add(['Button', $ac->getDescription()]), $ac, $field->jsInput()->val());
$btn->on('click', $ex);

// invoking this action requires argument "age" (integer). User should be prompted, end would return age in response
$ac = $country->addAction('edit_argument', ['args'=> ['age'=>['type'=>'integer', 'required' => true]], 'callback'=>function ($m, $age) {
    return 'age = '.$age;
}]);
$ex = $app->add(new UserAction(['stepTitle' => ['args' => ['Header', 'Age is required to perform this action:', 'size' => 5]]]))->setAction($ac);
$ex->assignTrigger($buttons->add(['Button', $ac->getDescription()]), [$ex->name => $field->jsInput()->val()]);

// invoking this action requires argument "age" (integer). User should be prompted, end would return age in response
$ac = $country->addAction('edit_argument_prev', ['args'=> ['age'=>['type'=>'integer', 'required' => true]], 'preview'=> function ($m, $age) {
    return 'You age is: '.$age;
}, 'callback'=>function ($m, $age) {
    return 'age = '.$age;
}]);
$ex = $app->add(new UserAction(['stepTitle' => ['args' => ['Header', 'Age is required to perform this action:', 'size' => 5]]]))->setAction($ac);
$ex->assignTrigger($buttons->add(['Button', $ac->getDescription()]), [$ex->name => $field->jsInput()->val()]);

// user can edit 'iso3' field before action is invoked (will be blank, since it's not loaded but should show proper label). NOT SAVING! but will still show 'ok' in toast
$ac = $country->addAction('edit_iso', ['fields'=> ['iso3'], 'callback'=>function () {
    return 'ok';
}]);
$ex = $app->add(new UserAction(['stepTitle' => ['fields' => ['Header', 'Fields:', 'size' => 5]]]))->setAction($ac);
$ex->assignTrigger($buttons->add(['Button', $ac->getDescription()]), [$ex->name => $field->jsInput()->val()]);

// if action throws exception, need to properly display to user
$ac = $country->addAction('Ouch', ['args'=> ['age'=>['type'=>'integer']], 'preview'=> function () {
    return 'Be careful with this action.';
}, 'callback'=> function () {
    throw new \atk4\ui\Exception('Told you, didn\'t I?');
}]);
$ex = $app->add(new UserAction())->setAction($ac);
$ex->assignTrigger($buttons->add(['Button', $ac->getDescription()]), [$ex->name => $field->jsInput()->val()]);

// action may require confirmation, before activating
$ac = $country->addAction('confirm', ['ui' => ['confirm'=>'Call action?'], 'callback'=>function () {
    return 'Confirm ok';
}]);
$ex = new jsEvent($btn = $buttons->add(['Button', $ac->getDescription()]), $ac, $field->jsInput()->val());
$btn->on('click', $ex, ['confirm' => $ac->ui['confirm']]);

// action may require confirmation, before activating
$ac = $country->addAction('multi_step', ['args'=> ['age'=>['type'=>'integer', 'required'=> true], 'gender' => ['type'=> 'enum', 'values' => ['m' => 'Male', 'f' => 'Female'], 'required'=>true]], 'fields'=> ['iso3'], 'callback'=> function ($m, $age, $gender) {
//    $m->save();
    return 'ok';
}, 'preview'=> function ($m, $age, $gender) {
    return 'Gender = '.$gender.' / Age = '.$age;
}]);
$ex = $app->add(new UserAction(['stepTitle' => ['args' => ['Header', 'Fill in argument:', 'size' => 5]]]))->setAction($ac);
$ex->assignTrigger($buttons->add(['Button', $ac->getDescription()]), [$ex->name => $field->jsInput()->val()]);

//foreach ($country->getActions() as $action) {
//    $ex = $app->add(new \atk4\ui\ActionExecutor\UserAction(['stepTitle' => ['args' => ['Header', 'Fill in argument:', 'size' => 5]]]))->setAction($action);
//    $ex->assignTrigger($buttons->add(['Button', $action->getDescription()]), [$ex->name => $field->jsInput()->val()]);
//}
