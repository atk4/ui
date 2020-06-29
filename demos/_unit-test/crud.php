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

$m = new CountryLock($app->db);
$edit = $m->getUserAction('edit');
$edit->ui = ['execButton' => [\atk4\ui\Button::class, 'EditMe', 'blue']];
$edit->description = 'edit';

$delete = $m->getUserAction('delete');
$delete->ui = [];
$delete->description = 'delete';

$add = $m->getUserAction('add');
$add->ui = ['execButton' => [\atk4\ui\Button::class, 'AddMe', 'blue']];
$add->description = 'Add';

$g = \atk4\ui\CRUD::addTo($app, ['ipp' => 10, 'menu' => ['class' => ['atk-grid-menu']]]);
$g->setModel($m);

$g->addQuickSearch(['name'], true);
