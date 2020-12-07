<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Button::addTo($app, ['Card Model', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['card-action']);
\Atk4\Ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\Atk4\Ui\Header::addTo($app, ['Card.', 'size' => 1, 'subHeader' => 'Component based on Fomantic-Ui Card view.']);

// *** Simple Card **/

\Atk4\Ui\Header::addTo($app, ['Card can be defined manually.', 'size' => 3]);

$card = \Atk4\Ui\Card::addTo($app);

$card->addContent((new \Atk4\Ui\Header(['Meet Kristy', 'subHeader' => 'Friends'])));

$card->addDescription('Kristy is a friend of Mully.');
$card->addImage('../images/kristy.png');

$card->addButton(new \Atk4\Ui\Button(['Join']));
$card->addButton(new \Atk4\Ui\Button(['Email']));

$card->addExtraContent(new \Atk4\Ui\View(['Copyright notice: Image from Semantic-UI (Fomantic-UI)', 'element' => 'span']));

// *** Simple Card **/

$card = \Atk4\Ui\Card::addTo($app);
$content = new \Atk4\Ui\View(['class' => ['content']]);
$content->add($img = new \Atk4\Ui\Image(['../images/kristy.png']));
$img->addClass('right floated mini ui image');
$content->add($header = new \Atk4\Ui\Header(['Kristy']));

$card->addContent($content);
$card->addDescription('Friend of Bob');

// **** Card with Table and Label***/

\Atk4\Ui\Header::addTo($app, ['Card can display model label in a table or in line.', 'size' => 3]);

$deck = \Atk4\Ui\View::addTo($app, ['ui' => 'cards']);

$cardStat = \Atk4\Ui\Card::addTo($deck, ['useTable' => true]);
$cardStat->addContent(new \Atk4\Ui\Header(['Project Info']));
$stats = (new Stat($app->db))->tryLoadAny();

$cardStat->setModel($stats, ['project_name', 'project_code', 'client_name', 'start_date']);

$btn = $cardStat->addButton(new \Atk4\Ui\Button(['Email Client']));

$cardStat = \Atk4\Ui\Card::addTo($deck, ['useLabel' => true]);
$cardStat->addContent(new \Atk4\Ui\Header(['Project Info']));
$stats = (new Stat($app->db))->tryLoadAny();

$cardStat->setModel($stats, ['project_name', 'project_code', 'client_name', 'start_date']);

$cardStat->addButton(new \Atk4\Ui\Button(['Email Client']));

// **** Card display horizontally ***/

\Atk4\Ui\Header::addTo($app, ['Card can be display horizontally and/or centered.', 'size' => 3]);

$card = \Atk4\Ui\Card::addTo($app)->addClass('horizontal centered');

$card->addContent((new \Atk4\Ui\Header(['Meet Kristy', 'subHeader' => 'Friends'])));
$card->addDescription('Kristy is a friend of Mully.');
$card->addImage('../images/kristy.png');
