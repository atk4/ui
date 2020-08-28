<?php

declare(strict_types=1);
/**
 * For Behat testing only.
 * see vue.feature.
 */

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new Country($app->db);

$search = \atk4\ui\Component\ItemSearch::addTo($app, ['inputTimeOut' => 0]);
$lister_container = \atk4\ui\View::addTo($app);
$lister = \atk4\ui\Lister::addTo($lister_container, ['defaultTemplate' => 'lister.html']);

$search->reload = $lister_container;
$lister->setModel($search->setModelCondition($model))->setLimit(10);
