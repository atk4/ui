<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Button::addTo($app, ['Loader Example - page 1', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['loader']);
\Atk4\Ui\View::addTo($app, ['ui' => 'ui clearing divider']);

$c = \Atk4\Ui\Columns::addTo($app);

$grid = \Atk4\Ui\Grid::addTo($c->addColumn(), ['ipp' => 10, 'menu' => false]);
$grid->setModel(new Country($app->db), ['name']);

$countryLoader = \Atk4\Ui\Loader::addTo($c->addColumn(), ['loadEvent' => false, 'shim' => [\Atk4\Ui\Text::class, 'Select country on your left']]);

$grid->table->onRowClick($countryLoader->jsLoad(['id' => $grid->table->jsRow()->data('id')]));

$countryLoader->set(function ($p) {
    \Atk4\Ui\Form::addTo($p)->setModel(new Country($p->getApp()->db))->load($_GET['id']);
});
