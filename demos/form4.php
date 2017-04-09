<?php
/**
 * Testing form.
 */
require 'init.php';
require 'database.php';

$layout->add(['Header', 'Form automatically decided how many columns to use']);

$form = $layout->add(['Form']);
$form->setModel(new Country($db));

$form->onSubmit(function ($form) {
    return $form->success('validation is ok');

    $errors = [];
    foreach ($form->model->dirty as $field => $value) {
        $errors[] = $form->error($field, 'Value was changed');
    }

    return $errors ?: $form->success('No changed fields');
});
