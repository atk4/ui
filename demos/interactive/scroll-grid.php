<?php

chdir('..');
require_once 'init.php';
require_once 'database.php';

\atk4\ui\Button::addTo($app, ['Dynamic scroll in Container', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['scroll-container']);
\atk4\ui\Button::addTo($app, ['Dynamic scroll in Grid using Container', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['scroll-grid-container']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Dynamic scroll in Grid']);

$g = \atk4\ui\Grid::addTo($app, ['menu' => false]);
$m = $g->setModel(new Country($db));

$g->addJsPaginator(30);
