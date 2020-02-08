<?php

require __DIR__ . '/init.php';
require __DIR__ . '/database.php';

$app->add(['Button', 'Dynamic scroll in Lister', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['scroll-lister']);
$app->add(['Button', 'Dynamic scroll in Container', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['scroll-container']);
$app->add(['View', 'ui' => 'ui clearing divider']);

$app->add(['Header', 'Dynamic scroll in Table']);

$table = $app->add(['Table']);

$m = $table->setModel(new Country($db));
//$m->addCondition('name','like','A%');

$table->addJsPaginator(30);
