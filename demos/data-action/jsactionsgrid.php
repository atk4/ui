<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Core\Factory;
use Atk4\Data\Model\UserAction;
use Atk4\Ui\Icon;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
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
$app->getExecutorFactory()->registerTrigger($app->getExecutorFactory()::TABLE_MENU_ITEM, $specialItem, $multiAction);

\Atk4\Ui\Header::addTo($app, ['Execute model action from Grid menu items', 'subHeader' => 'Setting grid menu items in order to execute model actions or javascript.']);

$grid = \Atk4\Ui\Grid::addTo($app, ['menu' => false]);
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
    $grid->addExecutorMenuItem($executor = $app->getExecutorFactory()->create($action, $grid));
}

$grid->ipp = 10;
