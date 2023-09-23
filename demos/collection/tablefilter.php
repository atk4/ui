<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Grid;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// for popup positioning to work correctly, table needs to be inside a view segment
$view = View::addTo($app, ['ui' => 'basic segment']);
$grid = Grid::addTo($view, ['menu' => ['class' => ['atk-grid-menu']]]); // menu class added for Behat testing

$model = new Country($app->db);
$model->addExpression('is_uk', [
    'expr' => $model->expr('case when [atk_fp_country__iso] = [country] THEN 1 ELSE 0 END', ['country' => 'GB']),
    'type' => 'boolean',
]);

$grid->setModel($model);
$grid->addFilterColumn();

$grid->ipp = 20;
