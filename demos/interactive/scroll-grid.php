<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Button::addTo($app, ['Dynamic scroll in Container', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['scroll-container']);
\Atk4\Ui\Button::addTo($app, ['Dynamic scroll in Grid using Container', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['scroll-grid-container']);
\Atk4\Ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\Atk4\Ui\Header::addTo($app, ['Dynamic scroll in Grid']);

$grid = \Atk4\Ui\Grid::addTo($app, ['menu' => false]);
$model = $grid->setModel(new CountryLock($app->db));

$grid->addJsPaginator(30);
