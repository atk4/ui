<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Card;
use Atk4\Ui\Example;
use Atk4\Ui\GridLayout;
use Atk4\Ui\Header;
use Atk4\Ui\Message;
use Atk4\Ui\UserAction\ExecutorFactory;
use Atk4\Ui\UserAction\PanelExecutor;
use Atk4\Ui\View;

// Demo for Model action

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

require_once __DIR__ . '/../../src/Example/Inc.php';

$country = new Country($app->db);

// replace default edit UA with custom UA /w ui property
$country->removeUserAction('edit');
// workaround seeding /w a different UA class, Model::addUserAction does not support seed class
\Closure::bind(fn () => $country->_defaultSeedUserAction[0] = Example\CustomUserAction::class, null, Model::class)();
$country->addUserAction('edit', [
    'fields' => true,
    'modifier' => Model\UserAction::MODIFIER_UPDATE,
    'appliesTo' => Model\UserAction::APPLIES_TO_SINGLE_RECORD,
    'callback' => 'save',
    'ui' => [
        'executor' => [Example\CustomModalExecutor::class, 'formSeed' => [Example\CustomForm::class]],
    ],
]);

// replace ExecutorFactory with custom factory /w ui['executor'] seed support
$app->setExecutorFactory(new Example\CustomExecutorFactory());
$app->getExecutorFactory()->registerTypeExecutor(ExecutorFactory::STEP_EXECUTOR, [PanelExecutor::class]);

$entity = $country->loadAny();
$countryId = $entity->getId();

// Model actions for this file are setup in DemoActionUtil.
DemoActionsUtil::setupDemoActions($country);

Header::addTo($app, ['Assign Model action to button event', 'subHeader' => 'Execute model action on this country record by clicking on the appropriate button on the right.']);

$msg = Message::addTo($app, ['Notes', 'type' => 'info']);
$msg->text->addParagraph('When passing an action to a button event, Ui will determine what executor is required base on the action properties.');
$msg->text->addParagraph('If action require arguments, fields and/or preview, then a ModalExecutor will be use.');

View::addTo($app, ['ui' => 'ui clearing divider']);

$gl = GridLayout::addTo($app, ['rows' => 1, 'columns' => 2]);
$c = Card::addTo($gl, ['useLabel' => true], ['r1c1']);
$c->addContent(new Header(['Using country: ']));
$c->setModel($entity, [$country->fieldName()->iso, $country->fieldName()->iso3, $country->fieldName()->phonecode]);

$buttons = View::addTo($gl, ['ui' => 'vertical basic buttons'], ['r1c2']);

// Create a button for every action in Country model.
foreach ($country->getUserActions() as $action) {
    $b = Button::addTo($buttons, [$action->getCaption()]);
    // Assign action to button using current model id as url arguments.
    $b->on('click', $action, ['args' => ['id' => $countryId]]);
}
