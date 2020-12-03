<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new Stat($app->db, ['caption' => 'Demo Stat']);
$model->addCondition('finish_time', '=', '22:12:00');
$model->addCondition('start_date', '=', '2020-10-22');

$form = \Atk4\Ui\Form::addTo($app);

$form->addControl('qb', [\Atk4\Ui\Form\Control\ScopeBuilder::class, 'model' => $model, 'options' => ['debug' => true]]);

$form->onSubmit(function ($form) use ($model) {
    return "Scope selected:\n\n" . $form->model->get('qb')->toWords($model);
});
