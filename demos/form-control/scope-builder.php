<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new Stat($app->db, ['caption' => 'Demo Stat']);
$model->addCondition('finish_time', '=', '22:12:00');
$model->addCondition('start_date', '=', '2020-10-22');

$form = \atk4\ui\Form::addTo($app);

$form->addControl('qb', [\atk4\ui\Form\Control\ScopeBuilder::class, 'model' => $model, 'options' => ['debug' => true]]);

$form->onSubmit(function ($form) use ($model) {
    return "Scope selected:\n\n" . $form->model->get('qb')->toWords($model);
});
