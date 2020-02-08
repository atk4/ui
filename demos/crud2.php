<?php

require __DIR__ . '/init.php';
require __DIR__ . '/database.php';

$g = $app->add(['CRUD']);
$g->setModel(new Stat($db));
$g->addDecorator('project_code', 'Link');
