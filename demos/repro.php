<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Crud;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/init-app.php';

$country = new Country($app->db);

$crud = Crud::addTo($app);
$crud->displayFields = [$country->fieldName()->name, $country->fieldName()->iso];
$crud->addFields = $crud->displayFields;
$crud->editFields = $crud->displayFields;
$crud->setModel($country);
$crud->addJsPaginatorInContainer(20, 500 /* $containerHeight */);
