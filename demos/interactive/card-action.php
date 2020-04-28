<?php

require_once __DIR__ . '/../atk-init.php';

\atk4\ui\Button::addTo($app, ['Card Deck', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['card-deck']);
\atk4\ui\Button::addTo($app, ['Card', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['card']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Models', 'size' => 1, 'subHeader' => 'Card may display information from many models.']);

$stats = new Stat($db);
$stats->loadAny();

$c = \atk4\ui\Card::addTo($app);
$c->setModel($stats, ['client_name', 'description']);

$c->addSection('Project: ', $stats, ['start_date', 'finish_date'], true);

$client = $stats->ref('client_country_iso')->loadAny();
$notify = $client->addAction('Notify', [
    'args' => [
        'note' => ['type' => 'string', 'required' => true],
    ],
    'callback' => function ($m) {
        return 'Note to client is sent.';
    },
]);
$c->addSection('Client Country:', $client, ['iso', 'numcode', 'phonecode'], true);

$c->addClickAction($notify, null, [$client->get('id')]);
