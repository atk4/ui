<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new Stat($app->db);
$model->getUserAction('add')->system = true;
$model->getUserAction('edit')->system = true;
$model->getUserAction('delete')->system = true;

$grid = \atk4\ui\Crud::addTo($app, ['paginator' => false]);
$grid->setModel($model);
$grid->addDecorator('project_code', [\atk4\ui\Table\Column\Link::class]);
