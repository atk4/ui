<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\JsToast;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new Country($app->db);
$model = $model->loadBy($model->fieldName()->iso, 'cz');

$saveAndDumpValues = function (Form $form) {
    $form->model->save();

    return new JsToast([
        'message' => $form->getApp()->encodeJson($form->model->get(Country::hinting()->fieldName()->name)),
        'class' => 'success',
        'displayTime' => 750,
    ]);
};

$form = Form::addTo($app);
$form->setModel($model, []);

$accordionLayout = $form->layout->addSubLayout([Form\Layout\Section\Accordion::class]);

$a1 = $accordionLayout->addSection('Section 1');
$a1->setModel($model, [$model->fieldName()->iso]);

$form->onSubmit($saveAndDumpValues);
