<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form\Control\Line;
use Atk4\Ui\UserAction;
use Atk4\Ui\UserAction\JsCallbackExecutor;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Header::addTo($app, [
    'Extensions to ATK Data Actions',
    'subHeader' => 'Model action can be trigger in various ways.',
]);

// Model action setup.
$country = new Country($app->db);

$sendEmailAction = $country->addUserAction('Email', [
    'confirmation' => 'Are you sure you wish to send an email?',
    'callback' => function ($model) {
        return 'Email to Kristy in ' . $model->get('name') . ' has been sent!';
    },
]);

///////////////////////////////////////////

\Atk4\Ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\Atk4\Ui\Header::addTo($app, [
    'Using Input button',
    'size' => 4,
    'subHeader' => 'Action can be triggered via a button attached to an input. The data action argument value is set to the input value.',
]);

// Note here that we explicitly required a JsCallbackExecutor for the greet action.
$country->addUserAction('greet', [
    'args' => [
        'name' => [
            'type' => 'string',
            'required' => true,
        ],
    ],
    'ui' => ['executor' => [UserAction\JsCallbackExecutor::class]],
    'callback' => function ($model, $name) {
        return 'Hello ' . $name;
    },
]);

// Set the action property for the Line Form Control.
Line::addTo($app, ['action' => $country->getUserAction('greet')]);

///////////////////////////////////////////

\Atk4\Ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\Atk4\Ui\Header::addTo($app, [
    'Using buttons in a Card component',
    'size' => 4,
    'subHeader' => 'Easily trigger a data action using a Card component.',
]);

// Card component.
$card = \Atk4\Ui\Card::addTo($app);
$content = new \Atk4\Ui\View(['class' => ['content']]);
$content->add($img = new \Atk4\Ui\Image(['../images/kristy.png']));
$img->addClass('right floated mini ui image');
$content->add(new \Atk4\Ui\Header(['Kristy']));

$card->addContent($content);
$card->addDescription('Kristy is a friend of Mully.');

$s = $card->addSection('Country');
$s->addFields($country->loadAny(), ['name', 'iso']);

// Pass the model action to the Card::addClickAction() method.
$card->addClickAction($sendEmailAction);
