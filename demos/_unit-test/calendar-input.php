<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Form;
use atk4\ui\View;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$output = function (string $date) {
    $view = new \atk4\ui\Message();
    $view->invokeInit();
    $view->text->addHTML($date);

    return $view;
};

\atk4\ui\Header::addTo($app, ['Testing flatpickr using Behat']);
$form = Form::addTo($app);
$c = $form->addControl('field', null, ['type' => 'date']);
$form->buttonSave->set($c->short_name);

$form->onSubmit(function ($form) use ($output, $c, $app) {
    return $output($form->model->get($c->short_name)->format($app->ui_persistence->date_format));
});

View::addTo($app, ['ui' => 'hidden divider']);
$app->ui_persistence->date_format = 'Y-m-d';
$form = Form::addTo($app);
$c = $form->addControl('date_ymd', [Form\Control\Calendar::class, 'type' => 'date']);
$form->buttonSave->set($c->short_name);

$form->onSubmit(function ($form) use ($output, $c) {
    return $output($form->model->get($c->short_name));
});

View::addTo($app, ['ui' => 'hidden divider']);
$app->ui_persistence->time_format = 'H:i:s';
$form = Form::addTo($app);
$c = $form->addControl('time_24hr', [Form\Control\Calendar::class, 'type' => 'time']);
$form->buttonSave->set($c->short_name);

$form->onSubmit(function ($form) use ($output, $c) {
    return $output($form->model->get($c->short_name));
});

View::addTo($app, ['ui' => 'hidden divider']);
$app->ui_persistence->time_format = 'G:i A';
$form = Form::addTo($app);
$c = $form->addControl('time_am', [Form\Control\Calendar::class, 'type' => 'time']);
$form->buttonSave->set($c->short_name);

$form->onSubmit(function ($form) use ($output, $c) {
    return $output($form->model->get($c->short_name));
});

View::addTo($app, ['ui' => 'hidden divider']);
$app->ui_persistence->datetime_format = 'Y-m-d (H:i:s)';
$form = Form::addTo($app);
$c = $form->addControl('datetime', [Form\Control\Calendar::class, 'type' => 'datetime']);
$form->buttonSave->set($c->short_name);

$form->onSubmit(function ($form) use ($output, $c) {
    return $output($form->model->get($c->short_name));
});
