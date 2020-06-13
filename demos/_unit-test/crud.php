<?php
/**
 * For Behat testing only.
 * Will test for Add, Edit and delete button using quicksearch.
 * see crud.feature.
 */

namespace atk4\ui\demo;

require_once __DIR__ . '/../atk-init.php';

$m = new CountryLock($db);
$edit = $m->getAction('edit');
$edit->ui = ['execButton' => [\atk4\ui\Button::class, 'EditMe', 'blue']];
$edit->description = 'edit';

$delete = $m->getAction('delete');
$delete->ui = [];
$delete->description = 'delete';

$add = $m->getAction('add');
$add->ui = ['execButton' => [\atk4\ui\Button::class, 'AddMe', 'blue']];
$add->description = 'Add';

$g = \atk4\ui\CRUD::addTo($app, ['ipp' => 10, 'menu' => ['class' => ['atk-grid-menu']]]);
$g->setModel($m);

$g->addQuickSearch(['name'], true);
