<?php
/**
 * Testing form.
 */
require __DIR__ . '/init.php';
require __DIR__ . '/database.php';

use atk4\ui\jsReload;

$app->add(['Header', 'Form automatically decided how many columns to use']);

$buttons = $app->add(['View', 'ui' => 'green basic buttons']);

$seg = $app->add(['View', 'ui' => 'raised segment']);

$buttons->add(['Button', 'Use Country Model', 'icon' => 'arrow down'])
    ->on('click', new jsReload($seg, ['m' => 'country']));
$buttons->add(['Button', 'Use File Model', 'icon' => 'arrow down'])
    ->on('click', new jsReload($seg, ['m' => 'file']));
$buttons->add(['Button', 'Use Stat Model', 'icon' => 'arrow down'])
    ->on('click', new jsReload($seg, ['m' => 'stat']));

$form = $seg->add(['Form', 'layout' => 'Columns']);
$form->setModel(
    isset($_GET['m']) ? (
        $_GET['m'] == 'country' ? new Country($db) : (
            $_GET['m'] == 'file' ? new File($db) : new Stat($db)
        )) : new Stat($db)
    )->tryLoadAny();

$form->onSubmit(function ($form) {
    $errors = [];
    foreach ($form->model->dirty as $field => $value) {
        // we should care only about editable fields
        if ($form->model->getField($field)->isEditable()) {
            $errors[] = $form->error($field, 'Value was changed, '.json_encode($value).' to '.json_encode($form->model[$field]));
        }
    }

    return $errors ?: 'No fields were changed';
});
