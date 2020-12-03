<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Message;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Demo for Model action

$country = new CountryLock($app->db);
$country->tryLoadAny();
$countryId = $country->get('id');

// Model actions for this file are setup in DemoActionUtil.
DemoActionsUtil::setupDemoActions($country);

\Atk4\Ui\Header::addTo($app, ['Assign Model action to button event', 'subHeader' => 'Execute model action on this country record by clicking on the appropriate button on the right.']);

$msg = Message::addTo($app, ['Notes', 'type' => 'info']);
$msg->text->addParagraph('When passing an action to a button event, Ui will determine what executor is required base on the action properties.');
$msg->text->addParagraph('If action require arguments, fields and/or preview, then a ModalExecutor will be use.');

\Atk4\Ui\View::addTo($app, ['ui' => 'ui clearing divider']);

$gl = \Atk4\Ui\GridLayout::addTo($app, ['rows' => 1, 'columns' => 2]);
$c = \Atk4\Ui\Card::addTo($gl, ['useLabel' => true], ['r1c1']);
$c->addContent(new \Atk4\Ui\Header(['Using country: ']));
$c->setModel($country, ['iso', 'iso3', 'phonecode']);

$buttons = \Atk4\Ui\View::addTo($gl, ['ui' => 'vertical basic buttons'], ['r1c2']);

$country->unload();
// Create a button for every action in Country model.
foreach ($country->getUserActions() as $action) {
    $b = \Atk4\Ui\Button::addTo($buttons, [$action->getCaption()]);
    // Assign action to button using current model id as url arguments.
    $b->on('click', $action, ['args' => ['id' => $countryId]]);
}
