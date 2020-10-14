<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Form;
use atk4\ui\JsExpression;
use atk4\ui\JsFunction;
use atk4\ui\JsToast;
use atk4\ui\Persistence\Type\Date;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$form = Form::addTo($app);

Date::setFormat('date', 'Y-m-d');
$form->addControl('date_y_m_d', [Form\Control\Calendar::class, 'type' => 'date', 'caption' => 'Date (Y-m-d)'])
    ->set(date(Date::getFormat('date')));

Date::setFormat('time', 'G:i A');
$form->addControl('time_g_i_a', [Form\Control\Calendar::class, 'type' => 'time', 'caption' => 'Time using am/pm'])
    ->set(date(Date::getFormat('time')));

Date::setFormat('time', 'H:i:s');
$form->addControl('time_h_i_s', [Form\Control\Calendar::class, 'type' => 'time', 'caption' => 'Time using 24 hrs with seconds picker'])
    ->set(date(Date::getFormat('time')));

$form->addControl('datetime', [Form\Control\Calendar::class, 'type' => 'datetime', 'caption' => 'Datetime (M d, Y H:i)'])
    ->set(date(Date::getFormat('datetime')));

Date::setFormat('date', 'F d, Y');
$form->addControl('date_f_d_y', [
    Form\Control\Calendar::class,
    'type' => 'date',
    'caption' => 'Allow input (F d, Y)',
    'options' => ['allowInput' => true],
])->set(date(Date::getFormat('date')));

Date::setFormat('date', 'Y-m-d');
$form->addControl('date_js_format', [
    Form\Control\Calendar::class,
    'type' => 'date',
    'caption' => 'Format via Javascript',
    'options' => [
        'formatDate' => new JsFunction(['date', 'format'], [new JsExpression('return "Date selected: " + flatpickr.formatDate(date, format)')]),
    ],
])->set(date(Date::getFormat('date')));

$form->addControl('date_range', [
    Form\Control\Calendar::class,
    'type' => 'date',
    'caption' => 'Range mode',
    'options' => ['mode' => 'range'],
])->set(date(Date::getFormat('date')) . ' to ' . date(Date::getFormat('date'), strtotime('+1 Week')));

$form->addControl('date_multi', [
    Form\Control\Calendar::class,
    'type' => 'date',
    'caption' => 'Multiple mode',
    'options' => ['mode' => 'multiple'],
])->set(date(Date::getFormat('date')) . ', ' . date(Date::getFormat('date'), strtotime('+1 Day')) . ', ' . date(Date::getFormat('date'), strtotime('+2 Day')));

$control = $form->addControl('date_action', [
    Form\Control\Calendar::class,
    'type' => 'date',
    'caption' => 'Javascript action',
    'options' => ['clickOpens' => false],
])->set(date(Date::getFormat('date')));
$control->addAction(['Today', 'icon' => 'calendar day'])->on('click', $control->getJsInstance()->setDate(date(Date::getFormat('date'))));
$control->addAction(['Select...', 'icon' => 'calendar'])->on('click', $control->getJsInstance()->open());
$control->addAction(['Clear', 'icon' => 'times red'])->on('click', $control->getJsInstance()->clear());

$form->onSubmit(function ($f) use ($app) {
    return new JsToast($app->encodeJson($f->model->get()));
});
