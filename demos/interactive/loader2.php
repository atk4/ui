<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

\atk4\ui\Button::addTo($app, ['Loader Example - page 1', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['loader']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

$c = \atk4\ui\Columns::addTo($app);

$grid = \atk4\ui\Grid::addTo($c->addColumn(), ['ipp' => 10, 'menu' => false]);
$grid->setModel(new Country($app->db), ['name']);

$countryLoader = \atk4\ui\Loader::addTo($c->addColumn(), ['loadEvent' => false, 'shim' => [\atk4\ui\Text::class, 'Select country on your left']]);

$grid->table->onRowClick($countryLoader->jsLoad(['id' => $grid->table->jsRow()->data('id')]));

$countryLoader->set(function ($p) {
    \atk4\ui\Form::addTo($p)->setModel(new Country($p->app->db))->load($_GET['id']);
});
