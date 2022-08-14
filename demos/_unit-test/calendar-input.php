<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$output = function (?\DateTime $dt, string $format) {
    $view = new \Atk4\Ui\Message();
    $view->invokeInit();
    $view->text->addHtml($dt === null ? 'empty' : $dt->format($format));

    return $view;
};

\Atk4\Ui\Header::addTo($app, ['Testing flatpickr using Behat']);
$form = Form::addTo($app);
$c = $form->addControl('field', [], ['type' => 'date']);
$form->buttonSave->set($c->shortName);

$form->onSubmit(function (Form $form) use ($output, $c, $app) {
    return $output($form->model->get($c->shortName), $app->uip->date_format);
});

View::addTo($app, ['ui' => 'hidden divider']);
$app->uip->date_format = 'Y-m-d';
$form = Form::addTo($app);
$c = $form->addControl('date_ymd', [Form\Control\Calendar::class, 'type' => 'date']);
$form->buttonSave->set($c->shortName);

$form->onSubmit(function (Form $form) use ($output, $c, $app) {
    return $output($form->model->get($c->shortName), $app->uip->date_format);
});

View::addTo($app, ['ui' => 'hidden divider']);
$app->uip->time_format = 'H:i:s';
$form = Form::addTo($app);
$c = $form->addControl('time_24hr', [Form\Control\Calendar::class, 'type' => 'time']);
$form->buttonSave->set($c->shortName);

$form->onSubmit(function (Form $form) use ($output, $c, $app) {
    return $output($form->model->get($c->shortName), $app->uip->time_format);
});

View::addTo($app, ['ui' => 'hidden divider']);
$app->uip->time_format = 'G:i A';
$form = Form::addTo($app);
$c = $form->addControl('time_am', [Form\Control\Calendar::class, 'type' => 'time']);
$form->buttonSave->set($c->shortName);

$form->onSubmit(function (Form $form) use ($output, $c, $app) {
    return $output($form->model->get($c->shortName), $app->uip->time_format);
});

View::addTo($app, ['ui' => 'hidden divider']);
$app->uip->datetime_format = 'Y-m-d (H:i:s)';
$form = Form::addTo($app);
$c = $form->addControl('datetime', [Form\Control\Calendar::class, 'type' => 'datetime']);
$form->buttonSave->set($c->shortName);

$form->onSubmit(function (Form $form) use ($output, $c, $app) {
    return $output($form->model->get($c->shortName), $app->uip->datetime_format);
});
