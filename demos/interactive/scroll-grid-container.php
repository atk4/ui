<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Button::addTo($app, ['Dynamic scroll in Crud and Grid', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['scroll-grid']);
\Atk4\Ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\Atk4\Ui\Header::addTo($app, ['Dynamic scroll in Grid with fixed column headers']);

$c = \Atk4\Ui\Columns::addTo($app);

$c1 = $c->addColumn();
$g1 = \Atk4\Ui\Crud::addTo($c1);
$m1 = $g1->setModel(new CountryLock($app->db));
$g1->addQuickSearch(['name', 'iso']);

// demo for additional action buttons in Crud + JsPaginator
$g1->addModalAction(['icon' => [\Atk4\Ui\Icon::class, 'cogs']], 'Details', function ($p, $id) use ($g1) {
    \Atk4\Ui\Card::addTo($p)->setModel($g1->model->load($id));
});
$g1->addActionButton('red', function ($js) {
    return $js->closest('tr')->css('color', 'red');
});
// THIS SHOULD GO AFTER YOU CALL addAction() !!!
$g1->addJsPaginatorInContainer(30, 350);

$c2 = $c->addColumn();
$g2 = \Atk4\Ui\Grid::addTo($c2, ['menu' => false]);
$m2 = $g2->setModel(new CountryLock($app->db));
$g2->addJsPaginatorInContainer(20, 200);

$g3 = \Atk4\Ui\Grid::addTo($c2, ['menu' => false]);
$m3 = $g3->setModel(new CountryLock($app->db));
$g3->addJsPaginatorInContainer(10, 150);
