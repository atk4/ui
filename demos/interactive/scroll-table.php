<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\Table;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Button::addTo($app, ['Dynamic scroll in Lister', 'class.small left floated basic blue' => true, 'icon' => 'left arrow'])
    ->link(['scroll-lister']);
Button::addTo($app, ['Dynamic scroll in Container', 'class.small right floated basic blue' => true, 'iconRight' => 'right arrow'])
    ->link(['scroll-container']);
View::addTo($app, ['ui' => 'clearing divider']);

Header::addTo($app, ['Dynamic scroll in Table']);

$table = Table::addTo($app);

$model = new Country($app->db);
$table->setModel($model);

$table->addJsPaginator(30);
