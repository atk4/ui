<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Card;
use Atk4\Ui\Columns;
use Atk4\Ui\Crud;
use Atk4\Ui\Grid;
use Atk4\Ui\Header;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Button::addTo($app, ['Dynamic scroll in Crud and Grid', 'class.small left floated basic blue' => true, 'icon' => 'left arrow'])
    ->link(['scroll-grid']);
View::addTo($app, ['ui' => 'clearing divider']);

Header::addTo($app, ['Dynamic scroll in Grid with fixed column headers']);

$c = Columns::addTo($app);

$c1 = $c->addColumn();
$g1 = Crud::addTo($c1);
$m1 = new Country($app->db);
$g1->setModel($m1);
$g1->addQuickSearch([Country::hinting()->fieldName()->name, Country::hinting()->fieldName()->iso]);

// demo for additional action buttons in Crud + JsPaginator
$g1->addModalAction(['icon' => 'cogs'], 'Details', static function (View $p, $id) use ($g1) {
    Card::addTo($p)->setModel($g1->model->load($id));
});
$g1->addActionButton('red', static function (Jquery $js) {
    return $js->closest('tr')->css('color', 'red');
});
// THIS SHOULD GO AFTER YOU CALL Grid::addActionButton()
$g1->addJsPaginatorInContainer(30, 350);

$c2 = $c->addColumn();
$g2 = Grid::addTo($c2, ['menu' => false]);
$m2 = new Country($app->db);
$g2->setModel($m2);
$g2->addJsPaginatorInContainer(20, 200);

$g3 = Grid::addTo($c2, ['menu' => false]);
$m3 = new Country($app->db);
$g3->setModel($m3);
$g3->addJsPaginatorInContainer(10, 150);
