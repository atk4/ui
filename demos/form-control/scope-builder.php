<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Form;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$model = new Stat($app->db, ['caption' => 'Demo Stat']);
$model->addCondition($model->fieldName()->finish_time, '=', new \DateTime('22:12:00'));
$model->addCondition($model->fieldName()->start_date, '=', new \DateTime('2020-10-22'));

$form = Form::addTo($app);

$form->addControl('qb', [Form\Control\ScopeBuilder::class, 'model' => $model,
    'options' => ['fieldFilter' => ['editable', 'system'], 'addAllReferencedFields' => true, 'debug' => true]]);

$form->onSubmit(static function (Form $form) {
    return "Scope selected:\n\n" . $form->model->get('qb');
});
