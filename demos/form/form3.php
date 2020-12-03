<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\JsReload;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Testing form.

\Atk4\Ui\Header::addTo($app, ['Form automatically decided how many columns to use']);

$buttons = \Atk4\Ui\View::addTo($app, ['ui' => 'green basic buttons']);

$seg = \Atk4\Ui\View::addTo($app, ['ui' => 'raised segment']);

\Atk4\Ui\Button::addTo($buttons, ['Use Country Model', 'icon' => 'arrow down'])
    ->on('click', new JsReload($seg, ['m' => 'country']));
\Atk4\Ui\Button::addTo($buttons, ['Use File Model', 'icon' => 'arrow down'])
    ->on('click', new JsReload($seg, ['m' => 'file']));
\Atk4\Ui\Button::addTo($buttons, ['Use Stat Model', 'icon' => 'arrow down'])
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
            $errors[] = $form->error($field, 'Value was changed, ' . $form->getApp()->encodeJson($value) . ' to ' . $form->getApp()->encodeJson($form->model->get($field)));
        }
    }

    return $errors ?: 'No fields were changed';
});
