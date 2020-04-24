<?php
/**
 * Testing form.
 */
chdir('..');

require_once 'atk-init.php';




use atk4\ui\jsReload;

\atk4\ui\Header::addTo($app, ['Form automatically decided how many columns to use']);

$buttons = \atk4\ui\View::addTo($app, ['ui' => 'green basic buttons']);

$seg = \atk4\ui\View::addTo($app, ['ui' => 'raised segment']);

\atk4\ui\Button::addTo($buttons, ['Use Country Model', 'icon' => 'arrow down'])
    ->on('click', new jsReload($seg, ['m' => 'country']));
\atk4\ui\Button::addTo($buttons, ['Use File Model', 'icon' => 'arrow down'])
    ->on('click', new jsReload($seg, ['m' => 'file']));
\atk4\ui\Button::addTo($buttons, ['Use Stat Model', 'icon' => 'arrow down'])
    ->on('click', new jsReload($seg, ['m' => 'stat']));

$form = \atk4\ui\Form::addTo($seg, ['layout' => 'Columns']);
$form->setModel(
    isset($_GET['m']) ? (
        $_GET['m'] == 'country' ? new Country($db) : (
            $_GET['m'] == 'file' ? new File($db) : new Stat($db)
        )
    ) : new Stat($db)
)->tryLoadAny();

$form->onSubmit(function ($form) {
    $errors = [];
    foreach ($form->model->dirty as $field => $value) {
        // we should care only about editable fields
        if ($form->model->getField($field)->isEditable()) {
            $errors[] = $form->error($field, 'Value was changed, ' . json_encode($value) . ' to ' . json_encode($form->model[$field]));
        }
    }

    return $errors ?: 'No fields were changed';
});
