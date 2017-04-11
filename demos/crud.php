<?php

require 'init.php';
require 'database.php';

$g = $layout->add(['CRUD', 'ops'=>['c'=>false]]);
$g->setModel(new Country($db));
