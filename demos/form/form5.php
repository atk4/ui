<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\JsToast;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\View::addTo($app, [
    'Forms below focus on Data integration and automated layouts',
    'ui' => 'ignored warning message',
]);

$formSubmit = function ($f) use ($app) {
    return new JsToast($app->encodeJson($f->model->get()));
};

$cc = \Atk4\Ui\Columns::addTo($app);
$form = Form::addTo($cc->addColumn());

// adding field without model creates a regular line
$form->addControl('one');

// Second argument string is used as a caption
$form->addControl('two', 'Caption');

// Array second is a default seed for default line field
$form->addControl('three', ['caption' => 'Caption2']);

// Use zeroth argument of the seed to specify standard class
$form->addControl('four', [Form\Control\Checkbox::class, 'caption' => 'Caption2']);

// Use explicit object for user-defined or 3rd party field
$form->addControl('five', new Form\Control\Checkbox())->set(true);

// Objects still accept seed
$form->addControl('six', new Form\Control\Checkbox(['caption' => 'Caption3']));

$form->onSubmit($formSubmit);

$model = new \Atk4\Data\Model(new \Atk4\Data\Persistence\Array_());

// model field uses regular line form control by default
$model->addField('one');

// caption is a top-level property of a field
$model->addField('two', ['caption' => 'Caption']);

// ui can also specify caption which is a form-specific
$model->addField('three', ['ui' => ['form' => ['caption' => 'Caption']]]);

// type is converted into CheckBox form control with caption as a seed
$model->addField('four', ['type' => 'boolean', 'ui' => ['form' => ['caption' => 'Caption2']]]);

// Can specify class for a checkbox explicitly
$model->addField('five', ['ui' => ['form' => [Form\Control\Checkbox::class, 'caption' => 'Caption3']]]);

// Form-specific caption overrides general caption of a field. Also you can specify object instead of seed
$model->addField('six', ['caption' => 'badcaption', 'ui' => ['form' => new Form\Control\Checkbox(['caption' => 'Caption4'])]]);

$form = Form::addTo($cc->addColumn());
$form->setModel($model);
$form->onSubmit($formSubmit);

// Next form won't initalize default fields, but we'll add them individually
$form = Form::addTo($cc->addColumn());
$form->setModel($model, false);

// adding that same field but with custom form control seed
$form->addControl('one', ['caption' => 'Caption0']);

// another way to override caption
$form->addControl('two', 'Caption2');

// We can override type, but seed from model will still be respected
$form->addControl('three', [Form\Control\Checkbox::class]);

// We override type and caption here
$form->addControl('four', [Form\Control\Line::class, 'caption' => 'CaptionX']);

// We can specify form control object. It's still seeded with caption from model.
$form->addControl('five', new Form\Control\Checkbox());

// can add field that does not exist in a model
$form->addControl('nine', new Form\Control\Checkbox(['caption' => 'Caption3']));
$form->onSubmit($formSubmit);
