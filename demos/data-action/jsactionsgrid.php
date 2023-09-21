<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Core\Factory;
use Atk4\Data\Model\UserAction;
use Atk4\Ui\Grid;
use Atk4\Ui\Header;
use Atk4\Ui\Icon;
use Atk4\Ui\UserAction\ExecutorFactory;
use Atk4\Ui\View;

// Demo for Model action in Grid

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$country = new Country($app->db);

// model actions for this file are setup in DemoActionUtil
DemoActionsUtil::setupDemoActions($country);

// creating special menu item for multi_step action
$multiAction = $country->getUserAction('multi_step');
$specialItem = Factory::factory([View::class], ['name' => false, 'class' => ['item'], 'content' => 'Multi Step']);
Icon::addTo($specialItem, ['content' => 'window maximize outline']);
// register this menu item in factory
$app->getExecutorFactory()->registerTrigger(ExecutorFactory::TABLE_MENU_ITEM, $specialItem, $multiAction);

Header::addTo($app, ['Execute model action from Grid menu items', 'subHeader' => 'Setting grid menu items in order to execute model actions or javascript.']);

$grid = Grid::addTo($app, ['menu' => false]);
$grid->setModel($country);

$divider = Factory::factory([View::class], ['name' => false, 'class' => ['divider'], 'content' => '']);

$modelHeader = Factory::factory([View::class], ['name' => false, 'class' => ['header'], 'content' => 'Model Actions']);
Icon::addTo($modelHeader, ['content' => 'database']);

$jsHeader = Factory::factory([View::class], ['name' => false, 'class' => ['header'], 'content' => 'JS Actions']);
Icon::addTo($jsHeader, ['content' => 'file code']);

$grid->addActionMenuItem($jsHeader);
// beside model user action, grid menu items can also execute javascript
$grid->addActionMenuItem('JS Callback', static function () {
    return (new View())->set('JS Callback done!');
}, 'Are you sure?');

$grid->addActionMenuItem($divider);

$grid->addActionMenuItem($modelHeader);

// adding Model actions
foreach ($country->getUserActions(UserAction::APPLIES_TO_SINGLE_RECORD) as $action) {
    if (in_array($action->shortName, ['add', 'edit', 'delete'], true)) {
        continue;
    }
    $grid->addExecutorMenuItem($executor = $app->getExecutorFactory()->createExecutor($action, $grid));
}

$grid->ipp = 10;
