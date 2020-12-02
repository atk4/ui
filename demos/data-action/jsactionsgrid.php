<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\core\Factory;
use atk4\data\Model\UserAction;
use atk4\ui\Icon;
use atk4\ui\UserAction\ExecutorFactory;
use atk4\ui\View;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Demo for Model action in Grid

$country = new CountryLock($app->db);
// Model actions for this file are setup in DemoActionUtil.
DemoActionsUtil::setupDemoActions($country);

// creating special menu item for multi_step action.
$multiAction = $country->getUserAction('multi_step');
$specialItem = Factory::factory([View::class], ['id' => false, 'class' => ['item'], 'content' => 'Multi Step']);
Icon::addTo($specialItem, ['content' => 'window maximize outline']);
// register this menu item in factory.
ExecutorFactory::registerActionTrigger(ExecutorFactory::TABLE_MENU_ITEM, $specialItem, $multiAction);

\atk4\ui\Header::addTo($app, ['Execute model action from Grid menu items', 'subHeader' => 'Setting grid menu items in order to execute model actions or javascript.']);

$grid = \atk4\ui\Grid::addTo($app, ['menu' => false]);
$grid->setModel($country);

$divider = Factory::factory([View::class], ['id' => false, 'class' => ['divider'], 'content' => '']);

$modelHeader = Factory::factory([View::class], ['id' => false, 'class' => ['header'], 'content' => 'Model Actions']);
Icon::addTo($modelHeader, ['content' => 'database']);

$jsHeader = Factory::factory([View::class], ['id' => false, 'class' => ['header'], 'content' => 'Js Actions']);
Icon::addTo($jsHeader, ['content' => 'file code']);

$grid->addActionMenuItem($jsHeader);
// Beside model user action, grid menu items can also execute javascript.
$grid->addActionMenuItem('Js Callback', function () {
    return (new View())->set('Js Callback done!');
}, 'Are you sure?');

$grid->addActionMenuItem($divider);

$grid->addActionMenuItem($modelHeader);

// Adding Model actions.
foreach ($country->getUserActions(UserAction::APPLIES_TO_SINGLE_RECORD) as $action) {
    if (in_array($action->short_name, ['add', 'edit', 'delete'], true)) {
        continue;
    }
    $grid->addExecutorMenuItem($executor = ExecutorFactory::create($action, $grid));
}

$grid->ipp = 10;
