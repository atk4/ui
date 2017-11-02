<?php

require 'init.php';
require 'database.php';

$g = $app->add(['CRUD']);
$g->setModel(new Stat($db));
$g->addDecorator('project_code', 'Link');
