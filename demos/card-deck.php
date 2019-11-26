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
    'callback' => function ($m, $city) {
        return 'Your request to visit '.ucwords($city).' in '.$m->get('name').' was sent!';
    },
    'ui' => ['button'=>[null, 'icon'=>'plane']]
]);

$action->args = ['city' => ['type'=>'string', 'required'=>true, 'caption' => 'Arrive at which city:']];

$countries->addAction('book_all', [
    'callback' => function ($m) {
        return 'Your request to visit all coutries was sent!';
    },
    'scope' => 'none',
    'ui' => ['button' => ['Request All','ui' => 'button primary', 'icon' => 'plane'], 'confirm' => 'Are you sure?'],
    ]);


$deck = $app->add('CardDeck');

$deck->setModel($countries, ['Cost'], ['iso', 'iso3']);

