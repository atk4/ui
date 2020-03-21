<?php
/**
 * Testing form.
 */
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

\atk4\ui\Header::addTo($app, ['Form automatically decided how many columns to use']);

$form = \atk4\ui\Form::addTo($app);
$form->setModel(new Country($db));

$form->onSubmit(function ($form) {
    return $form->success('validation is ok');

    $errors = [];
    foreach ($form->model->dirty as $field => $value) {
        $errors[] = $form->error($field, 'Value was changed');
    }

    return $errors ?: $form->success('No changed fields');
});
