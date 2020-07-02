<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Button;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

\atk4\ui\Button::addTo($app, ['Card Deck', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['card-deck']);
\atk4\ui\Button::addTo($app, ['Card', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['card']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Models', 'size' => 1, 'subHeader' => 'Card may display information from many models.']);

$stats = new Stat($app->db);
$stats->loadAny();

$c = \atk4\ui\Card::addTo($app);
$c->setModel($stats, ['client_name', 'description']);

$c->addSection('Project: ', $stats, ['start_date', 'finish_date'], true);

$client = $stats->ref('client_country_iso')->loadAny();
$notify = $client->addUserAction('Notify', [
    'args' => [
        'note' => ['type' => 'string', 'required' => true],
    ],
    'callback' => function ($model, $note) {
        return 'Note to client is sent: ' . $note;
    },
]);
$c->addSection('Client Country:', $client, ['iso', 'numcode', 'phonecode'], true);

$c->addClickAction($notify, new Button(['Send Note']), [$client->get('id')]);
