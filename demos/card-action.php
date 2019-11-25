<?php

require 'init.php';
require 'database.php';

$app->add(['Button', 'Card Deck', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['card-deck']);
$app->add(['Button', 'Card', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['card']);
$app->add(['View', 'ui' => 'ui clearing divider']);


$app->add(['Header', 'Models', 'size' => 1, 'subHeader' => 'Card may display information from many models.']);

$stats = new Stat($db);

$stats->loadAny();

$c = $app->add('Card');

$c->setModel($stats, ['client_name', 'description']);

$c->addSection('Project: ', $stats, ['start_date', 'finish_date'], true);

$client = $stats->ref('client_country_iso')->loadAny();
$notify = $client->addAction('Notify',
                   ['args' => [
                        'note'=> ['type'=>'string', 'required'=>true],
                   ],
                   'callback' => function($m) {
                        return 'Note to client is sent.';
                   },
]);

$c->addSection('Client Country:', $client, ['iso', 'numcode', 'phonecode'], true);

$c->addClickAction($notify, null, [$client->get('id')]);
