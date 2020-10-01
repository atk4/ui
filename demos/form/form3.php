<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Form;
use atk4\ui\JsReload;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Testing form.

\atk4\ui\Header::addTo($app, ['Form automatically decided how many columns to use']);

$buttons = \atk4\ui\View::addTo($app, ['ui' => 'green basic buttons']);

$seg = \atk4\ui\View::addTo($app, ['ui' => 'raised segment']);

\atk4\ui\Button::addTo($buttons, ['Use Country Model', 'icon' => 'arrow down'])
    ->on('click', new JsReload($seg, ['m' => 'country']));
\atk4\ui\Button::addTo($buttons, ['Use File Model', 'icon' => 'arrow down'])
    ->on('click', new JsReload($seg, ['m' => 'file']));
\atk4\ui\Button::addTo($buttons, ['Use Stat Model', 'icon' => 'arrow down'])
    ->on('click', new JsReload($seg, ['m' => 'stat']));

$form = Form::addTo($seg, ['layout' => [Form\Layout\Columns::class]]);
$form->setModel(
    isset($_GET['m']) ? (
        $_GET['m'] === 'country' ? new Country($app->db) : (
            $_GET['m'] === 'file' ? new File($app->db) : new Stat($app->db)
        )
    ) : new Stat($app->db)
)->tryLoadAny();

$form->onSubmit(function (Form $form) {
    $errors = [];
    foreach ($form->model->dirty as $field => $value) {
        // we should care only about editable fields
        if ($form->model->getField($field)->isEditable()) {
            $errors[] = $form->error($field, 'Value was changed, ' . $form->app->encodeJson($value) . ' to ' . $form->app->encodeJson($form->model->get($field)));
        }
    }

    return $errors ?: 'No fields were changed';
});
