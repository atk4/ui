<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

//For popup positioning to work correctly, table need to be inside a view segment.
$view = \atk4\ui\View::addTo($app, ['ui' => 'basic segment']);
// Important: menu class added for Behat testing.
$g = \atk4\ui\Grid::addTo($view, ['menu' => ['class' => ['atk-grid-menu']]]);

$m = new CountryLock($app->db);
$m->addExpression('is_uk', $m->expr('if([iso] = [country], 1, 0)', ['country' => 'GB']))->type = 'boolean';

$g->setModel($m);
$g->addFilterColumn();

$g->ipp = 20;
