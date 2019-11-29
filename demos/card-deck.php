<?php

require 'init.php';
require 'database.php';

$app->add(['Button', 'Card Model', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['card-action']);
$app->add(['View', 'ui' => 'ui clearing divider']);

$app->add(['Header', 'Card Deck', 'size' => 1, 'subHeader' => 'Card can be display in a deck, also using model action.']);

$countries = new Country($db);
$countries->addCalculatedField('Cost', function ($m) {
    return '$ '.number_format(rand(500, 1500));
});

$action = $countries->addAction('book', [
    'callback' => function ($m, $city, $email) {
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
    'country'=> ['required' => true, 'ui' => ['form' => ['AutoComplete', 'model'=> new Country($db), 'placeholder' => 'Please select a country.']]],
];

$deck = $app->add(['CardDeck', 'noRecordScopeActions' => ['request_info'], 'singleScopeActions' => ['book']]);

$deck->setModel($countries, ['Cost'], ['iso', 'iso3']);
