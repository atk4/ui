<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Button::addTo($app, ['Card Deck', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['card-deck']);
\Atk4\Ui\Button::addTo($app, ['Card', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['card']);
\Atk4\Ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\Atk4\Ui\Header::addTo($app, ['Models', 'size' => 1, 'subHeader' => 'Card may display information from many models.']);

$stats = new Stat($app->db);
$stats->loadAny();

$c = \Atk4\Ui\Card::addTo($app);
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
