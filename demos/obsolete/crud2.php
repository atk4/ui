<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new Stat($app->db);
$model->getUserAction('add')->system = true;
$model->getUserAction('edit')->system = true;
$model->getUserAction('delete')->system = true;

$grid = \Atk4\Ui\Crud::addTo($app, ['paginator' => false]);
$grid->setModel($model);
$grid->addDecorator('project_code', [\Atk4\Ui\Table\Column\Link::class]);
