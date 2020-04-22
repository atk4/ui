<?php

chdir('..');
require_once 'init.php';
require_once 'database.php';

$g = \atk4\ui\CRUD::addTo($app);
$g->setModel(new Stat($db));
$g->addDecorator('project_code', 'Link');
