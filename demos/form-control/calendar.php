<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\Js\JsToast;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$demo = Demo::addTo($app, ['leftWidth' => 10, 'rightWidth' => 6]);

$form = Form::addTo($demo->left);

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

$form->onSubmit(static function (Form $form) use ($app) {
    $data = [];
    foreach ($form->model->get() as $k => $v) {
        $data[$k] = $v !== null
            ? $app->uiPersistence->typecastSaveField($form->model->getField($k), $v)
            : 'empty';
    }

    return new JsToast(implode(', ', $data));
});
