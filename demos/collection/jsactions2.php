<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Demo for Model action

$country = new CountryLock($app->db);
$country->tryLoadAny();
DemoActionsUtil::setupDemoActions($country);

\atk4\ui\Button::addTo($app, ['Actions in Grid', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['jsactionsgrid']);

\atk4\ui\Button::addTo($app, ['Action from Js Event', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['jsactions']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Model Custom Actions', 'subHeader' => 'Execute model action on this country record by clicking on the appropriate button on the right.']);

$gl = \atk4\ui\GridLayout::addTo($app, ['rows' => 1, 'columns' => 2]);
$c = \atk4\ui\Card::addTo($gl, ['useLabel' => true], ['r1c1']);
$c->addContent(new \atk4\ui\Header(['Using country: ']));
$c->setModel($country, ['iso', 'iso3', 'phonecode']);

$buttons = \atk4\ui\View::addTo($gl, ['ui' => 'vertical basic buttons'], ['r1c2']);

$country->unload();

// assign a button to every action
$countryId = $country->tryLoadAny()->get('id');
foreach ($country->getUserActions() as $action) {
    $b = \atk4\ui\Button::addTo($buttons, [$action->getDescription()]);
    $b->on('click', $action, ['args' => ['id' => $countryId]]);
}
