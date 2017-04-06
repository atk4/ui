<?php
/**
 * Testing form.
 */
require 'init.php';
require 'database.php';

$layout->add(['Header', 'Testing CRUD-friendly from implementation']);

$form = $layout->add(['Form', 'layout'=>'FormLayout/Columns']);
$form->setModel(new Stat($db))->loadAny();

$form->onSubmit(function ($form) {
    $errors = [];
    foreach ($form->model->dirty as $field => $value) {
        $errors[] = $form->error($field, 'Value was changed');
    }

    return $errors ?: $form->success('No changed fields');
});
