<?php

require 'init.php';
require 'database.php';

use atk4\ui\ActionExecutor\jsArgumentForm;
use atk4\ui\Form;

$country = new Country($db);

$app->add(new \atk4\ui\Header(['Enter Country model id', 'size' => 4]));
$field = $app->add(new \atk4\ui\FormField\Line(['caption' => 'Enter model id']))->set(12);

$btn_edit = $app->add(['Button', 'Edit']);
$btn_add = $app->add(['Button', 'Add New']);

$vp_edit = $app->add(['VirtualPage']);
$vp_add = $app->add(['VirtualPage']);

$form = new Form();
$form->addHook('formInit', function ($f) {
    // setup special form content.
});

$btn_edit->on('click', new jsArgumentForm($country->getAction('edit'), $vp_edit, $field->jsInput()->val(), $form));
$btn_add->on('click', new jsArgumentForm($country->getAction('add'), $vp_add));
