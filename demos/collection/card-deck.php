<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Card Deck', 'size' => 1, 'subHeader' => 'Card can be display in a deck, also using model action.']);

$countries = new Country($app->db);
$countries->addCalculatedField('Cost', function (Country $country) {
    return '$ ' . number_format(random_int(500, 1500));
});

$action = $countries->addUserAction('book', [
    'callback' => function (Country $country, $email, $city) {
        return 'Your request to visit ' . ucwords($city) . ' in ' . $country->name . ' was sent to: ' . $email;
    },
]);

// Create custom button for this action in card.
$app->getExecutorFactory()->registerTrigger($app->getExecutorFactory()::CARD_BUTTON, [Button::class, null, 'blue', 'icon' => 'plane'], $action);

$action->args = [
    'email' => ['type' => 'email', 'required' => true, 'caption' => 'Please let us know your email address:'],
    'city' => ['type' => 'string', 'required' => true, 'caption' => 'Arrive at which city:'],
];

$infoAction = $countries->addUserAction('request_info', [
    'callback' => function (Country $country, $email) {
        return 'Your request for information was sent to email: ' . $email;
    },
    'appliesTo' => \Atk4\Data\Model\UserAction::APPLIES_TO_NO_RECORDS,
]);

$infoAction->args = [
    'email' => ['type' => 'email', 'required' => true, 'caption' => 'Please let us know your email address:'],
    'country' => ['required' => true, 'ui' => ['form' => [\Atk4\Ui\Form\Control\Lookup::class, 'model' => new Country($app->db), 'placeholder' => 'Please select a country.']]],
];

$deck = \Atk4\Ui\CardDeck::addTo($app, ['noRecordScopeActions' => ['request_info'], 'singleScopeActions' => ['book']]);

$deck->setModel($countries, ['Cost'], [$countries->fieldName()->iso, $countries->fieldName()->iso3]);
