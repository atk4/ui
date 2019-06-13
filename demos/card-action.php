<?php

require 'init.php';
require 'database.php';

$app->add(['Button', 'Card', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['card']);
$app->add(['View', 'ui' => 'ui clearing divider']);

$app->add(['Header', 'Actions', 'size' => 1, 'subHeader' => 'Card may contain model action.']);

$countries = new Country($db);
$countries->addCalculatedField('Cost', function ($m) {
    return '$ '.number_format(rand(500, 1500));
});

$action = $countries->addAction('Book', function ($m) {
    return 'Your request to visit '.$m->get('name').' was sent!';
});

$action->args = ['city' => ['type'=>'string', 'required'=>true, 'caption' => 'Which City']];

$countries->setLimit(4);

$deck = $app->add('CardDeck');

$deck->setModel($countries, ['Cost']);

$app->add(['Header', 'Models', 'size' => 1, 'subHeader' => 'Card may display information from many models.']);

$stats = new Stat($db);

$stats->tryLoadAny();

$c = $app->add('Card');

$c->setModel($stats, ['client_name', 'description']);

$c->addSection('Project: ', $stats, ['start_date', 'finish_date'], true);

$c->addSection('Client Country:', $stats->ref('client_country_iso'), ['iso', 'numcode', 'phonecode'], true);
