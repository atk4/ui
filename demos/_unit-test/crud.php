<?php

declare(strict_types=1);
/**
 * For Behat testing only.
 * Will test for Add, Edit and delete button using quicksearch.
 * see crud.feature.
 */

namespace Atk4\Ui\Demos;

use Atk4\Ui\UserAction\ExecutorFactory;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// reset to default button
ExecutorFactory::useActionTriggerDefault(ExecutorFactory::TABLE_BUTTON);

$model = new CountryLock($app->db);
$crud = \Atk4\Ui\Crud::addTo($app, ['ipp' => 10, 'menu' => ['class' => ['atk-grid-menu']]]);
$crud->setModel($model);

$crud->addQuickSearch(['name'], true);
