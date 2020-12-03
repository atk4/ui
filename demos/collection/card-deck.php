<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Button::addTo($app, ['Card Model', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['card-action']);
\Atk4\Ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\Atk4\Ui\Header::addTo($app, ['Card Deck', 'size' => 1, 'subHeader' => 'Card can be display in a deck, also using model action.']);

$countries = new Country($app->db);
$countries->addCalculatedField('Cost', function ($model) {
    return '$ ' . number_format(random_int(500, 1500));
});

$action = $countries->addUserAction('book', [
    'callback' => function ($model, $email, $city) {
        return 'Your request to visit ' . ucwords($city) . ' in ' . $model->get('name') . ' was sent to: ' . $email;
    },
    'ui' => ['button' => [null, 'icon' => 'plane']],
]);

$action->args = [
    'email' => ['type' => 'email', 'required' => true, 'caption' => 'Please let us know your email address:'],
    'city' => ['type' => 'string', 'required' => true, 'caption' => 'Arrive at which city:'],
];

$infoAction = $countries->addUserAction('request_info', [
    'callback' => function ($model, $email) {
        return 'Your request for information was sent to email: ' . $email;
    },
    'appliesTo' => \Atk4\Data\Model\UserAction::APPLIES_TO_NO_RECORDS,
    'ui' => ['button' => ['Request Info', 'ui' => 'button primary', 'icon' => [\Atk4\Ui\Icon::class, 'mail']]],
]);

$infoAction->args = [
    'email' => ['type' => 'email', 'required' => true, 'caption' => 'Please let us know your email address:'],
    'country' => ['required' => true, 'ui' => ['form' => [\Atk4\Ui\Form\Control\Lookup::class, 'model' => new Country($app->db), 'placeholder' => 'Please select a country.']]],
];

$deck = \Atk4\Ui\CardDeck::addTo($app, ['noRecordScopeActions' => ['request_info'], 'singleScopeActions' => ['book']]);

$deck->setModel($countries, ['Cost'], ['iso', 'iso3']);
