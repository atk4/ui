<?php

namespace atk4\ui\demo;

require_once __DIR__ . '/../atk-init.php';

$m = new Stat($db);
$m->getAction('add')->system = true;
$m->getAction('edit')->system = true;
$m->getAction('delete')->system = true;

$g = \atk4\ui\CRUD::addTo($app, ['paginator' => false]);
$g->setModel($m);
$g->addDecorator('project_code', 'Link');
