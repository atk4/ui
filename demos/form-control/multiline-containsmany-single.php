<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Form;
use Atk4\Ui\Js\JsToast;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$form = Form::addTo($app);
$form->setEntity((new MultilineDelivery($app->db))->createEntity());
$form->onSubmit(static function (Form $form) {
    // save ContainsXxx data to JSON
    // https://github.com/atk4/data/blob/6.0.0/src/Reference/ContainsOne.php#L29-L40
    $form->entity->save();
    $form->entity->setNull($form->entity->idField);

    return new JsToast($form->getApp()->encodeJson($form->entity->get()));
});
