<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\Grid;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsToast;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$model = new Country($app->db);
$grid = Grid::addTo($app, ['ipp' => 5]);
$grid->setModel($model);

$grid->addSelection();

$grid->addBulkAction('Show selected', static function (Jquery $j, array $ids) use ($grid) {
    return new JsToast('Selected: ' . implode(', ', array_map(static fn ($id) => $grid->getApp()->uiPersistence->typecastSaveField($grid->model->getIdField(), $id), $ids)) . '#');
});
