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

$action = $countries->addAction('Book', function ($m, $city) {
    return 'Your request to visit '.ucwords($city).' in '.$m->get('name').' was sent!';
});

$action->args = ['city' => ['type'=>'string', 'required'=>true, 'caption' => 'Which City']];

$countries->setLimit(4);

$deck = $app->add('CardDeck');

$deck->setModel($countries, ['Cost']);
