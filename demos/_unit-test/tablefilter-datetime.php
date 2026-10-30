<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Grid;
use Atk4\Ui\View;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

// for popup positioning to work correctly, table needs to be inside a view segment
$view = View::addTo($app, ['ui' => 'basic segment']);
$grid = Grid::addTo($view, ['menu' => ['class' => ['atk-grid-menu']]]); // menu class added for Behat testing

$model = new Stat($app->db);
$model->getField($model->fieldName()->updated)->caption = 'Updated Datetime';
$grid->setModel($model, [
    $model->fieldName()->project_name, // string
    $model->fieldName()->project_budget, // atk4_money
    $model->fieldName()->currency, // list of string values
    $model->fieldName()->finish_date, // date
    $model->fieldName()->finish_time, // time
    $model->fieldName()->updated, // datetime
]);

$grid->addFilterColumn();

$grid->ipp = 20;
