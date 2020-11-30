<?php

declare(strict_types=1);
/**
 * For Behat testing only.
 * Will test for Add, Edit and delete button using quicksearch.
 * see crud.feature.
 */

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new CountryLock($app->db);
$model->getUserAction('edit')->ui = [];
$model->getUserAction('delete')->ui = [];

$crud = \atk4\ui\Crud::addTo($app, ['ipp' => 10, 'menu' => ['class' => ['atk-grid-menu']]]);
$crud->setModel($model);

$crud->addQuickSearch(['name'], true);
