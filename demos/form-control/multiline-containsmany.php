<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Crud;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$model = new MultilineDelivery($app->db);
$model->getField($model->fieldName()->item)->ui['visible'] = true;
$model->getField($model->fieldName()->items)->ui['visible'] = true;

$crud = Crud::addTo($app);
$crud->setModel($model);
