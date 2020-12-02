<?php

declare(strict_types=1);
/**
 * For Behat testing only.
 * Will test for Add, Edit and delete button using quicksearch.
 * see crud.feature.
 */

namespace atk4\ui\demo;

use atk4\ui\UserAction\ExecutorFactory;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

// reset to default button
ExecutorFactory::useActionTriggerDefault(ExecutorFactory::TABLE_BUTTON);

$model = new CountryLock($app->db);
$crud = \atk4\ui\Crud::addTo($app, ['ipp' => 10, 'menu' => ['class' => ['atk-grid-menu']]]);
$crud->setModel($model);

$crud->addQuickSearch(['name'], true);
