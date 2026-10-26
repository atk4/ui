<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Grid;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$grid = Grid::addTo($app);

$model = new Stat($app->db);
$grid->setModel($model, [
    $model->fieldName()->project_name, // string
    $model->fieldName()->project_code, // string
    $model->fieldName()->project_budget, // atk4_money
    $model->fieldName()->project_hours_est, // int
    $model->fieldName()->is_commercial, // bool
    $model->fieldName()->currency, // list of string values
    $model->fieldName()->finish_date, // date
    $model->fieldName()->finish_time, // time
    $model->fieldName()->updated, // datetime
]);

$grid->addFilterColumn();
