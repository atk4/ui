<?php

chdir('..');
require_once 'atk-init.php';

$g = \atk4\ui\CRUD::addTo($app);
$g->setModel(new Stat($db));
$g->addDecorator('project_code', 'Link');
