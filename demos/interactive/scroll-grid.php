<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Header;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Button::addTo($app, ['Dynamic scroll in Container', 'class.small left floated basic blue' => true, 'icon' => 'left arrow'])
    ->link(['scroll-container']);
\Atk4\Ui\Button::addTo($app, ['Dynamic scroll in Grid using Container', 'class.small right floated basic blue' => true, 'iconRight' => 'right arrow'])
    ->link(['scroll-grid-container']);
\Atk4\Ui\View::addTo($app, ['ui' => 'ui clearing divider']);

Header::addTo($app, ['Dynamic scroll in Grid']);

$grid = \Atk4\Ui\Grid::addTo($app, ['menu' => false]);
$model = new Country($app->db);
$grid->setModel($model);

$grid->addJsPaginator(30);
