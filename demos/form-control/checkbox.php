<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['CheckBoxes', 'size' => 2]);

Form\Control\Checkbox::addTo($app, ['Make my profile visible']);
Form\Control\Checkbox::addTo($app, ['Make my profile visible ticked'])->set(true);

View::addTo($app, ['ui' => 'divider']);
Form\Control\Checkbox::addTo($app, ['Accept terms and conditions', 'class.slider' => true]);

View::addTo($app, ['ui' => 'divider']);
Form\Control\Checkbox::addTo($app, ['Subscribe to weekly newsletter', 'class.toggle' => true]);
View::addTo($app, ['ui' => 'divider']);
Form\Control\Checkbox::addTo($app, ['Look for the clues', 'class.disabled toggle' => true])->set(true);

View::addTo($app, ['ui' => 'divider']);
Form\Control\Checkbox::addTo($app, ['Custom setting?'])->js(true)->checkbox('set indeterminate');

Header::addTo($app, ['CheckBoxes in a form', 'size' => 2]);
$form = Form::addTo($app);
$form->addControl('test', [Form\Control\Checkbox::class]);
$form->addControl('test_checked', [Form\Control\Checkbox::class])->set(1);
$form->addControl('also_checked', ['caption' => 'Also checked by default'], ['type' => 'boolean'])->set(true);

$form->onSubmit(static function (Form $form) use ($app) {
    return new JsToast($app->encodeJson($form->model->get()));
});
