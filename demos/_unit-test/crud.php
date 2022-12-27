<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Crud;
use Atk4\Ui\UserAction\ExecutorFactory;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$app->getExecutorFactory()->useTriggerDefault(ExecutorFactory::TABLE_BUTTON);

$model = new Country($app->db);
$crud = Crud::addTo($app, ['ipp' => 10, 'menu' => ['class' => ['atk-grid-menu']]]);
$crud->setModel($model);

$crud->addQuickSearch([$model->fieldName()->name, $model->fieldName()->phonecode], true);
