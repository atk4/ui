<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Button;
use atk4\ui\UserAction\ExecutorFactory;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

\atk4\ui\Button::addTo($app, ['Card Model', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['card-action']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Card Deck', 'size' => 1, 'subHeader' => 'Card can be display in a deck, also using model action.']);

$countries = new Country($app->db);
$countries->addCalculatedField('Cost', function ($model) {
    return '$ ' . number_format(random_int(500, 1500));
});

$action = $countries->addUserAction('book', [
    'callback' => function ($model, $email, $city) {
        return 'Your request to visit ' . ucwords($city) . ' in ' . $model->get('name') . ' was sent to: ' . $email;
    },
]);

// Create custom button for this action in card.
ExecutorFactory::registerActionTrigger(ExecutorFactory::CARD_BUTTON, [Button::class, null, 'blue', 'icon' => 'plane'], $action);

$action->args = [
    'email' => ['type' => 'email', 'required' => true, 'caption' => 'Please let us know your email address:'],
    'city' => ['type' => 'string', 'required' => true, 'caption' => 'Arrive at which city:'],
];

$infoAction = $countries->addUserAction('request_info', [
    'callback' => function ($model, $email) {
        return 'Your request for information was sent to email: ' . $email;
    },
    'appliesTo' => \atk4\data\Model\UserAction::APPLIES_TO_NO_RECORDS,
]);

$infoAction->args = [
    'email' => ['type' => 'email', 'required' => true, 'caption' => 'Please let us know your email address:'],
    'country' => ['required' => true, 'ui' => ['form' => [\atk4\ui\Form\Control\Lookup::class, 'model' => new Country($app->db), 'placeholder' => 'Please select a country.']]],
];

$deck = \atk4\ui\CardDeck::addTo($app, ['noRecordScopeActions' => ['request_info'], 'singleScopeActions' => ['book']]);

$deck->setModel($countries, ['Cost'], ['iso', 'iso3']);
