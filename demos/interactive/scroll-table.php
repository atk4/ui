<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../atk-init.php';

\atk4\ui\Button::addTo($app, ['Dynamic scroll in Lister', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['scroll-lister']);
\atk4\ui\Button::addTo($app, ['Dynamic scroll in Container', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['scroll-container']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Dynamic scroll in Table']);

$table = \atk4\ui\Table::addTo($app);

$m = $table->setModel(new Country($app->db));
//$m->addCondition('name','like','A%');

$table->addJsPaginator(30);
