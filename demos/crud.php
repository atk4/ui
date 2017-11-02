<?php

require 'init.php';
require 'database.php';

$g = $app->add(['CRUD']);
$g->setModel(new Country($db));
