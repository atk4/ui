<?php

require 'init.php';

$form = $app->add('Form');
//$g = $form->addGroup(['width' => 'three']);
$form->addField('name');

$form->addField('gender', ['DropDown', 'values' => ['Female', 'Male']]);
$form->addField('value');
$form->addField('surname');

//$form->js(true)->atkTest(['fieldRules' => [['surname' => ['name' => 'empty', 'gender'=>'is[1]', 'value'=>'integer[1..10]']], ['surname' => ['name' => 'isExactly[dog]']]]]);

//