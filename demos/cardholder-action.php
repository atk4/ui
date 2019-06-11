<?php

require 'init.php';
require 'database.php';

$app->add(['Button', 'CardHolder', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['cardholder']);
$app->add(['View', 'ui' => 'ui clearing divider']);

$app->add(['Header', 'Actions', 'size' => 1, 'subHeader' => 'CardHolder may contain model action.']);

$countries = new Country($db);
$countries->addCalculatedField('Cost', function ($m) {
    return '$ '.number_format(rand(500, 1500));
});

$countries->getField('Cost')->type = 'money';
$countries->addAction('Visit', function ($m) {
    return 'Your request to visit '.$m->get('name').' was sent!';
});

$countries->setLimit(4);

$deck = $app->add(['ui' => 'cards']);

$countries->each(function ($m) use ($deck) {
    $c = $deck->add(['CardHolder', 'useLabel' => true]);
    $c->setModel($m, ['name', 'Cost']);
});
