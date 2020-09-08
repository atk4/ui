<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Demo for Model action in Grid

$country = new CountryLock($app->db);
DemoActionsUtil::setupDemoActions($country);

\atk4\ui\Button::addTo($app, ['Actions from jsEvent', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['jsactions2']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Model Custom Actions', 'subHeader' => 'Model custom action can be execute from Grid.']);

$grid = \atk4\ui\Grid::addTo($app, ['menu' => false]);
$grid->setModel($country);

$divider = $app->factory([\atk4\ui\View::class], ['id' => false, 'class' => ['divider'], 'content' => '']);

$modelHeader = $app->factory([\atk4\ui\View::class], ['id' => false, 'class' => ['header'], 'content' => 'Model Actions']);
\atk4\ui\Icon::addTo($modelHeader, ['content' => 'database']);

$jsHeader = $app->factory([\atk4\ui\View::class], ['id' => false, 'class' => ['header'], 'content' => 'Js Actions']);
\atk4\ui\Icon::addTo($jsHeader, ['content' => 'file code']);

$grid->addActionMenuItem($jsHeader);
$grid->addActionMenuItem('Js Callback', function () {
    return (new \atk4\ui\View())->set('Js Callback done!');
}, 'Are you sure?');

$grid->addActionMenuItem($divider);

$grid->addActionMenuItem($modelHeader);
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
    ]
);

$specialItem = $app->factory([\atk4\ui\View::class], ['id' => false, 'class' => ['item'], 'content' => 'Multi Step']);
\atk4\ui\Icon::addTo($specialItem, ['content' => 'window maximize outline']);

$grid->addActionMenuItem($specialItem, $country->getUserAction('multi_step'));

$grid->ipp = 10;
