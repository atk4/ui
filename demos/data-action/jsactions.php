<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model\UserAction;
use Atk4\Ui\Card;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Image;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, [
    'Extensions to ATK Data Actions',
    'subHeader' => 'Model action can be trigger in various ways.',
]);

// Model action setup
$country = new Country($app->db);

$sendEmailAction = $country->addUserAction('Email', [
    'confirmation' => 'Are you sure you wish to send an email?',
    'callback' => static function (Country $country) {
        return 'Email to Kristy in ' . $country->name . ' has been sent!';
    },
]);

// -----------------------------------------------------------------------------

View::addTo($app, ['ui' => 'clearing divider']);

Header::addTo($app, [
    'Using Input button',
    'size' => 4,
    'subHeader' => 'Action can be triggered via a button attached to an input. The data action argument value is set to the input value.',
]);

// note here that we explicitly required a JsCallbackExecutor for the greet action
$country->addUserAction('greet', [
    'appliesTo' => UserAction::APPLIES_TO_NO_RECORDS,
    'args' => [
        'name' => [
            'type' => 'string',
            'required' => true,
        ],
    ],
    'callback' => static function (Country $model, string $name) {
        return 'Hello ' . $name;
    },
]);

// set the action property for the Line Form Control
Form\Control\Line::addTo($app, ['action' => $country->getUserAction('greet')]);

// -----------------------------------------------------------------------------

View::addTo($app, ['ui' => 'clearing divider']);

Header::addTo($app, [
    'Using buttons in a Card component',
    'size' => 4,
    'subHeader' => 'Easily trigger a data action using a Card component.',
]);

// Card component
$card = Card::addTo($app);
$content = new View(['class' => ['content']]);
$img = Image::addTo($content, ['../images/kristy.png']);
$img->addClass('right floated mini');
Header::addTo($content, ['Kristy']);

$card->addContent($content);
$card->addDescription('Kristy is a friend of Mully.');

$s = $card->addSection('Country');
$s->addFields($entity = $country->loadAny(), [$country->fieldName()->name, $country->fieldName()->iso]);

// pass the model action to the Card::addClickAction() method
$card->addClickAction($sendEmailAction, null, ['id' => $entity->getId()]);
