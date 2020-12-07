<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Button::addTo($app, ['Dynamic scroll in Lister', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['scroll-lister']);
\Atk4\Ui\Button::addTo($app, ['Dynamic scroll in Container', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['scroll-container']);
\Atk4\Ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\Atk4\Ui\Header::addTo($app, ['Dynamic scroll in Table']);

$table = \Atk4\Ui\Table::addTo($app);

$model = $table->setModel(new Country($app->db));
//$model->addCondition('name','like','A%');

$table->addJsPaginator(30);
