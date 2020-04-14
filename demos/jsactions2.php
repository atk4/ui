<?php

// Check this file in order to see how model action is defined.
require_once __DIR__ . '/country_actions.php';

\atk4\ui\Button::addTo($app, ['Actions in Grid', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['jsactionsgrid']);

\atk4\ui\Button::addTo($app, ['Action from Js Event', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['jsactions']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

$gl = \atk4\ui\GridLayout::addTo($app, ['rows' => 1, 'columns' => 2]);
$c = \atk4\ui\Card::addTo($gl, ['useLabel' => true], ['r1c1']);
$c->addContent(new \atk4\ui\Header(['Using country: ']));
$c->setModel($country, ['iso', 'iso3', 'phonecode']);

$buttons = \atk4\ui\View::addTo($gl, ['ui'=>'vertical basic buttons'], ['r1c2']);

$country->unload();

// assign a button to every action from country_actions.php file.
foreach ($c_actions as $action) {
    $b = \atk4\ui\Button::addTo($buttons, [$action->getDescription()]);
    $b->on('click', $action, ['args' => ['id' => $id]]);
}
