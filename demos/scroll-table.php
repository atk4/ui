<?php

require 'init.php';
require 'database.php';

$table = $app->add(['Table']);
$m = $table->setModel(new Country($db));
$table->addJsPaginator(30);
