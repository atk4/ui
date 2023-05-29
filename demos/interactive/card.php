<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Card;
use Atk4\Ui\Header;
use Atk4\Ui\Image;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Button::addTo($app, ['Card Model', 'class.small right floated basic blue' => true, 'iconRight' => 'right arrow'])
    ->link(['card-action']);
View::addTo($app, ['ui' => 'clearing divider']);

Header::addTo($app, ['Card.', 'size' => 1, 'subHeader' => 'Component based on Fomantic-UI Card view.']);

// Simple Card

Header::addTo($app, ['Card can be defined manually.', 'size' => 3]);

$card = Card::addTo($app);

$card->addContent(new Header(['Meet Kristy', 'subHeader' => 'Friends']));

$card->addDescription('Kristy is a friend of Mully.');
$card->addImage('../images/kristy.png');

$card->addButton(new Button(['Join']));
$card->addButton(new Button(['Email']));

$card->addExtraContent(new View(['Copyright notice: Image from Fomantic-UI', 'element' => 'span']));

// Simple Card

$card = Card::addTo($app);
$content = new View(['class' => ['content']]);
$img = Image::addTo($content, ['../images/kristy.png']);
$img->addClass('right floated mini');
$header = Header::addTo($content, ['Kristy']);

$card->addContent($content);
$card->addDescription('Friend of Bob');

// Card with Table and Label

Header::addTo($app, ['Card can display model label in a table or in line.', 'size' => 3]);

$deck = View::addTo($app, ['ui' => 'cards']);

$cardStat = Card::addTo($deck, ['useTable' => true]);
$cardStat->addContent(new Header(['Project Info']));
$stat = (new Stat($app->db))->loadAny();
$cardStat->setModel($stat, [$stat->fieldName()->project_name, $stat->fieldName()->project_code, $stat->fieldName()->client_name, $stat->fieldName()->start_date]);

$button = $cardStat->addButton(new Button(['Email Client']));

$cardStat = Card::addTo($deck, ['useLabel' => true]);
$cardStat->addContent(new Header(['Project Info']));
$stat = (new Stat($app->db))->loadAny();
$cardStat->setModel($stat, [$stat->fieldName()->project_name, $stat->fieldName()->project_code, $stat->fieldName()->client_name, $stat->fieldName()->start_date]);

$cardStat->addButton(new Button(['Email Client']));

// Card display horizontally

Header::addTo($app, ['Card can be display horizontally and/or centered.', 'size' => 3]);

$card = Card::addTo($app)->addClass('horizontal centered');

$card->addContent(new Header(['Meet Kristy', 'subHeader' => 'Friends']));
$card->addDescription('Kristy is a friend of Mully.');
$card->addImage('../images/kristy.png');
