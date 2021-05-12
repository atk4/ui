<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\GridLayout;
use Atk4\Ui\JsExpression;
use Atk4\Ui\JsFunction;
use Atk4\Ui\JsToast;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$layout = GridLayout::addTo($app, ['rows' => 1, 'columns' => 2]);

$form = Form::addTo($layout, [], ['r1c1']);

$app->ui_persistence->date_format = 'Y-m-d';
$form->addControl('date_y_m_d', [Form\Control\Calendar::class, 'type' => 'date', 'caption' => 'Date (Y-m-d)'])
    ->set(date('Y-m-d'));

$app->ui_persistence->time_format = 'G:i A';
$form->addControl('time_g_i_a', [Form\Control\Calendar::class, 'type' => 'time', 'caption' => 'Time using am/pm'])
    ->set(date('G:i A'));

$app->ui_persistence->time_format = 'H:i:s';
$form->addControl('time_h_i_s', [Form\Control\Calendar::class, 'type' => 'time', 'caption' => 'Time using 24 hrs with seconds picker'])
    ->set(date('H:i:s'));

$form->addControl('datetime', [Form\Control\Calendar::class, 'type' => 'datetime', 'caption' => 'Datetime (M d, Y H:i:s)'])
    ->set(date($app->ui_persistence->datetime_format));

$app->ui_persistence->date_format = 'F d, Y';
$form->addControl('date_f_d_y', [
    Form\Control\Calendar::class,
    'type' => 'date',
    'caption' => 'Allow input (F d, Y)',
    'options' => ['allowInput' => true],
])->set(date('F d, Y'));

$app->ui_persistence->date_format = 'Y-m-d';
$form->addControl('date_js_format', [
    Form\Control\Calendar::class,
    'type' => 'date',
    'caption' => 'Format via Javascript',
    'options' => [
        'formatDate' => new JsFunction(['date', 'format'], [new JsExpression('return "Date selected: " + flatpickr.formatDate(date, format)')]),
    ],
])->set(date('Y-m-d'));

$form->addControl('date_range', [
    Form\Control\Calendar::class,
    'type' => 'date',
    'caption' => 'Range mode',
    'options' => ['mode' => 'range'],
])->set(date('Y-m-d') . ' to ' . date('Y-m-d', strtotime('+1 Week')));

$form->addControl('date_multi', [
    Form\Control\Calendar::class,
    'type' => 'date',
    'caption' => 'Multiple mode',
    'options' => ['mode' => 'multiple'],
])->set(date('Y-m-d') . ', ' . date('Y-m-d', strtotime('+1 Day')) . ', ' . date('Y-m-d', strtotime('+2 Day')));

$control = $form->addControl('date_action', [
    Form\Control\Calendar::class,
    'type' => 'date',
    'caption' => 'Javascript action',
    'options' => ['clickOpens' => false],
])->set(date('Y-m-d'));
$control->addAction(['Today', 'icon' => 'calendar day'])->on('click', $control->getJsInstance()->setDate(date('Y-m-d')));
$control->addAction(['Select...', 'icon' => 'calendar'])->on('click', $control->getJsInstance()->open());
$control->addAction(['Clear', 'icon' => 'times red'])->on('click', $control->getJsInstance()->clear());

$form->onSubmit(function ($f) use ($app) {
    return new JsToast($app->encodeJson($f->model->get()));
});
