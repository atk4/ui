<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Card;
use Atk4\Ui\Header;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Button::addTo($app, ['Card', 'class.small left floated basic blue' => true, 'icon' => 'left arrow'])
    ->link(['card']);
View::addTo($app, ['ui' => 'clearing divider']);

Header::addTo($app, ['Models', 'size' => 1, 'subHeader' => 'Card may display information from many models.']);

$stat = new Stat($app->db);
$stat = $stat->loadAny();

$c = Card::addTo($app);
$c->setModel($stat, [$stat->fieldName()->client_name, $stat->fieldName()->description]);

$c->addSection('Project: ', $stat, [$stat->fieldName()->start_date, $stat->fieldName()->finish_date], true);

$country = $stat->client_country_iso;
$notify = $country->getModel()->addUserAction('Notify', [
    'args' => [
        'note' => ['type' => 'string', 'required' => true],
    ],
    'callback' => static function (Model $model, $note) {
        return 'Note to client is sent: ' . $note;
    },
]);
$c->addSection('Client Country:', $country, [$country->fieldName()->iso, $country->fieldName()->numcode, $country->fieldName()->phonecode], true);

$c->addClickAction($notify, new Button(['Send Note']), [$country->id]);
