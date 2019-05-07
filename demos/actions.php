<?php

require 'init.php';
require 'database.php';

$app->add(['Header', 'Extensions to ATK Data Actions', 'subHeader'=>'Demonstrate how to augment your models with actions and vizualize those inside UI']);

// Actions can be added easily to the model

$files = new File($app->db);

// This action must appear on top of the CRUD
$files->addAction('Import From Filesystem', 'importFromFilesystem', ['string'], ['scope'=>atk4\data\UserAction\Action::NO_RECORDS]);

$app->add(['CRUD', 'ipp'=>5])->setModel($files);
