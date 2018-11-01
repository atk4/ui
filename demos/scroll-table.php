<?php

require 'init.php';
require 'database.php';

$app->add(['Button', 'Dynamic scroll in container', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['scroll-container']);

$app->add(['View', 'ui' => 'ui clearing divider']);

$table = $app->add(['Table']);
$m = $table->setModel(new Country($db));
$table->addJsPaginator(30);
