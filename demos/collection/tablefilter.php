<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// For popup positioning to work correctly, table need to be inside a view segment.
$view = \Atk4\Ui\View::addTo($app, ['ui' => 'basic segment']);
// Important: menu class added for Behat testing.
$grid = \Atk4\Ui\Grid::addTo($view, ['menu' => ['class' => ['atk-grid-menu']]]);

$model = new CountryLock($app->db);
$model->addExpression('is_uk', $model->expr('case when [iso] = [country] THEN 1 ELSE 0 END', ['country' => 'GB']))->type = 'boolean';

$grid->setModel($model);
$grid->addFilterColumn();

$grid->ipp = 20;
