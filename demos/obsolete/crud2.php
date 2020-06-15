<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../atk-init.php';

$m = new Stat($app->db);
$m->getAction('add')->system = true;
$m->getAction('edit')->system = true;
$m->getAction('delete')->system = true;

$g = \atk4\ui\CRUD::addTo($app, ['paginator' => false]);
$g->setModel($m);
$g->addDecorator('project_code', \atk4\ui\TableColumn\Link::class);
