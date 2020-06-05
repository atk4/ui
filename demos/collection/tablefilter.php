<?php

namespace atk4\ui\demo;

require_once __DIR__ . '/../atk-init.php';

//For popup positioning to work correctly, table need to be inside a view segment.
$view = \atk4\ui\View::addTo($app, ['ui' => 'basic segment']);
// Important: menu class added for Behat testing.
$g = \atk4\ui\Grid::addTo($view, ['menu' => ['class' => ['atk-grid-menu']]]);

$m = new CountryLock($db);
$m->addExpression('is_uk', $m->expr('if([iso] = [country], 1, 0)', ['country' => 'GB']))->type = 'boolean';

$g->setModel($m);
$g->addFilterColumn();

$g->ipp = 20;
