<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Message;
use atk4\ui\UserAction\ConfirmationExecutor;
use atk4\ui\UserAction\ExecutorFactory;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Demo for Model action

$country = new CountryLock($app->db);
$country->tryLoadAny();
$countryId = $country->get('id');

// Model actions for this file are setup in DemoActionUtil.
DemoActionsUtil::setupDemoActions($country);
// Confirm action user a Confirmation executor.
ExecutorFactory::registerActionExecutor($country->getUserAction('confirm'), [ConfirmationExecutor::class]);

\atk4\ui\Header::addTo($app, ['Assign Model action to button event', 'subHeader' => 'Execute model action on this country record by clicking on the appropriate button on the right.']);

$msg = Message::addTo($app, ['Notes', 'type' => 'info']);
$msg->text->addParagraph('When passing an action to a button event, Ui will determine what executor is required base on the action properties.');
$msg->text->addParagraph('If action require arguments, fields and/or preview, then a ModalExecutor will be use.');

\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

$gl = \atk4\ui\GridLayout::addTo($app, ['rows' => 1, 'columns' => 2]);
$c = \atk4\ui\Card::addTo($gl, ['useLabel' => true], ['r1c1']);
$c->addContent(new \atk4\ui\Header(['Using country: ']));
$c->setModel($country, ['iso', 'iso3', 'phonecode']);

$buttons = \atk4\ui\View::addTo($gl, ['ui' => 'vertical basic buttons'], ['r1c2']);

$country->unload();
// Create a button for every action in Country model.
foreach ($country->getUserActions() as $action) {
    $b = \atk4\ui\Button::addTo($buttons, [$action->getCaption()]);
    // Assign action to button using current model id as url arguments.
    $b->on('click', $action, ['args' => ['id' => $countryId]]);
}
