<?php

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

$g = \atk4\ui\CRUD::addTo($app);
$g->setModel(new Stat($db));
$g->addDecorator('project_code', 'Link');
