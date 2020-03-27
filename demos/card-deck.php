<?php

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

\atk4\ui\Button::addTo($app, ['Card Model', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['card-action']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Card Deck', 'size' => 1, 'subHeader' => 'Card can be display in a deck, also using model action.']);

$countries = new Country($db);
$countries->addCalculatedField('Cost', function ($m) {
    return '$ '.number_format(rand(500, 1500));
});

$action = $countries->addAction('book', [
    'callback' => function ($m, $email, $city) {
        return 'Your request to visit '.ucwords($city).' in '.$m->get('name').' was sent to: '.$email;
    },
    'ui' => ['button'=>[null, 'icon'=>'plane']],
]);

$action->args = [
    'email' => ['type'=>'email', 'required'=>true, 'caption' => 'Please let us know your email address:'],
    'city'  => ['type'=>'string', 'required'=>true, 'caption' => 'Arrive at which city:'],
];

$info_action = $countries->addAction('request_info', [

    'callback' => function ($m, $email) {
        return 'Your request for information was sent to email: '.$email;
    },
    'scope' => 'none',
    'ui'    => ['button' => ['Request Info', 'ui' => 'button primary', 'icon' => 'mail']],
]);

$info_action->args = [
    'email'  => ['type'=>'email', 'required'=>true, 'caption' => 'Please let us know your email address:'],
    'country'=> ['required' => true, 'ui' => ['form' => ['Lookup', 'model'=> new Country($db), 'placeholder' => 'Please select a country.']]],
];

$deck = \atk4\ui\CardDeck::addTo($app, ['noRecordScopeActions' => ['request_info'], 'singleScopeActions' => ['book']]);

$deck->setModel($countries, ['Cost'], ['iso', 'iso3']);
