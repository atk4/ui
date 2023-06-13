<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\GridLayout;
use Atk4\Ui\Js\JsToast;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$layout = GridLayout::addTo($app, ['rows' => 1, 'columns' => 2]);

$form = Form::addTo($layout, [], ['r1c1']);

$form->addControl('date', [Form\Control\Calendar::class, 'type' => 'date'])
    ->set(new \DateTime());

$form->addControl('time', [Form\Control\Calendar::class, 'type' => 'time'])
    ->set(new \DateTime());

$form->addControl('datetime', [Form\Control\Calendar::class, 'type' => 'datetime'])
    ->set(new \DateTime());

$control = $form->addControl('date_action', [
    Form\Control\Calendar::class,
    'type' => 'date',
    'caption' => 'Date with actions',
    'options' => ['clickOpens' => false],
])->set(new \DateTime());
$control->addAction(['Today', 'icon' => 'calendar day'])
    ->on('click', $control->getJsInstance()->setDate($app->uiPersistence->typecastSaveField($control->entityField->getField(), new \DateTime())));
$control->addAction(['Select...', 'icon' => 'calendar'])
    ->on('click', $control->getJsInstance()->open());
$control->addAction(['Clear', 'icon' => 'times red'])
    ->on('click', $control->getJsInstance()->clear());

// TODO "date" type does not support ranges
// $form->addControl('date_range', [
//    Form\Control\Calendar::class,
//    'type' => 'date',
//    'options' => ['mode' => 'range'],
// ])->set(date('Y-m-d') . ' to ' . date('Y-m-d', strtotime('+1 week')));
//
// $form->addControl('date_multiple', [
//    Form\Control\Calendar::class,
//    'type' => 'date',
//    'options' => ['mode' => 'multiple'],
// ])->set(date('Y-m-d') . ', ' . date('Y-m-d', strtotime('+1 Day')) . ', ' . date('Y-m-d', strtotime('+2 Day')));

$form->onSubmit(function (Form $form) use ($app) {
    return new JsToast($app->encodeJson($form->model->get()));
});
