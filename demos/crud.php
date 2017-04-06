<?php

require 'init.php';
require 'database.php';

$g = $layout->add(['CRUD']);
$g->setModel(new Country($db));

