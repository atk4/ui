<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Core\Factory;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Demo for Model action in Grid

$country = new CountryLock($app->db);
// Model actions for this file are setup in DemoActionUtil.
DemoActionsUtil::setupDemoActions($country);

\Atk4\Ui\Header::addTo($app, ['Execute model action from Grid menu items', 'subHeader' => 'Setting grid menu items in order to execute model actions or javascript.']);

$grid = \Atk4\Ui\Grid::addTo($app, ['menu' => false]);
$grid->setModel($country);

$divider = Factory::factory([\Atk4\Ui\View::class], ['id' => false, 'class' => ['divider'], 'content' => '']);

$modelHeader = Factory::factory([\Atk4\Ui\View::class], ['id' => false, 'class' => ['header'], 'content' => 'Model Actions']);
\Atk4\Ui\Icon::addTo($modelHeader, ['content' => 'database']);

$jsHeader = Factory::factory([\Atk4\Ui\View::class], ['id' => false, 'class' => ['header'], 'content' => 'Js Actions']);
\Atk4\Ui\Icon::addTo($jsHeader, ['content' => 'file code']);

$grid->addActionMenuItem($jsHeader);
// Beside model user action, grid menu items can also execute javascript.
$grid->addActionMenuItem('Js Callback', function () {
    return (new \Atk4\Ui\View())->set('Js Callback done!');
}, 'Are you sure?');

$grid->addActionMenuItem($divider);

$grid->addActionMenuItem($modelHeader);
// Adding Model actions.
$grid->addActionMenuItems(
    [
        'callback',
        'preview',
        'disabled_action',
        'edit_argument',
        'edit_argument_prev',
        'edit_iso',
        'Ouch',
        'confirm',
        'multi_step',
    ]
);

$specialItem = Factory::factory([\Atk4\Ui\View::class], ['id' => false, 'class' => ['item'], 'content' => 'Multi Step']);
\Atk4\Ui\Icon::addTo($specialItem, ['content' => 'window maximize outline']);

$grid->ipp = 10;
