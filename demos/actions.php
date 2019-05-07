<?php

require 'init.php';
require 'database.php';

$app->add(['Header', 'Extensions to ATK Data Actions', 'subHeader'=>'Demonstrate how to augment your models with actions and vizualize those inside UI']);


// Actions can be added easily to the model

$files = new File($app->db);

$files->addAction('Import From Filesystem');


$app->add('CRUD')->setModel($files);
