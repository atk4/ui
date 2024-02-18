<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Columns;
use Atk4\Ui\Form;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\View;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

View::addTo($app, [
    'Forms below demonstrate how to work with multi-value selectors',
    'ui' => 'ignored warning message',
]);

$cc = Columns::addTo($app);
$form = Form::addTo($cc->addColumn());

$form->addControl('enum_d', [], ['enum' => ['female', 'male']])->set('male');
$form->addControl('enum_r', [Form\Control\Radio::class], ['enum' => ['female', 'male']])->set('male');

$form->addControl('list_d', [], ['type' => 'integer', 'values' => ['female', 'male']])->set(1);
$form->addControl('list_r', [Form\Control\Radio::class], ['type' => 'integer', 'values' => ['female', 'male']])->set(1);

$form->addControl('int_d', [], ['type' => 'integer', 'values' => [5 => 'female', 7000 => 'male']])->set(7000);
$form->addControl('int_r', [Form\Control\Radio::class], ['type' => 'integer', 'values' => [5 => 'female', 7000 => 'male']])->set(7000);

$form->addControl('string_d', [], ['values' => ['F' => 'female', 'M' => 'male']])->set('M');
$form->addControl('string_r', [Form\Control\Radio::class], ['values' => ['F' => 'female', 'M' => 'male']])->set('M');

$form->onSubmit(static function (Form $form) use ($app) {
    return new JsToast($app->encodeJson($form->getApp()->uiPersistence->typecastSaveRow($form->model, $form->model->get())));
});
