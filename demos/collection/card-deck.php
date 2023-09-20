<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\CardDeck;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\UserAction\ExecutorFactory;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Card Deck', 'size' => 1, 'subHeader' => 'Card can be display in a deck, also using model action.']);

$countries = new Country($app->db);
$countries->addCalculatedField('cost', ['type' => 'atk4_money', 'expr' => static function (Country $country) {
    return random_int(500, 1500);
}]);

$action = $countries->addUserAction('book', [
    'callback' => static function (Country $country, $email, $city) {
        return 'Your request to visit ' . ucwords($city) . ' in ' . $country->name . ' was sent to: ' . $email;
    },
]);

// create custom button for this action in card
$app->getExecutorFactory()->registerTrigger(ExecutorFactory::CARD_BUTTON, [Button::class, 'class.blue' => true, 'icon' => 'plane'], $action);

$action->args = [
    'email' => ['type' => 'string', 'required' => true, 'caption' => 'Please let us know your email address:'],
    'city' => ['type' => 'string', 'required' => true, 'caption' => 'Arrive at which city:'],
];

$infoAction = $countries->addUserAction('request_info', [
    'callback' => static function (Country $country, $email) {
        return 'Your request for information was sent to email: ' . $email;
    },
    'appliesTo' => Model\UserAction::APPLIES_TO_NO_RECORDS,
]);

$infoAction->args = [
    'email' => ['type' => 'string', 'required' => true, 'caption' => 'Please let us know your email address:'],
    'country' => ['required' => true, 'ui' => ['form' => [Form\Control\Lookup::class, 'model' => new Country($app->db), 'placeholder' => 'Please select a country.']]],
];

$deck = CardDeck::addTo($app, ['noRecordScopeActions' => ['request_info'], 'singleScopeActions' => ['book']]);

$deck->setModel($countries, ['cost'], [$countries->fieldName()->iso, $countries->fieldName()->iso3]);
