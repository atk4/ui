<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

\atk4\ui\Button::addTo($app, ['Card Model', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['card-action']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

\atk4\ui\Header::addTo($app, ['Card.', 'size' => 1, 'subHeader' => 'Component based on Fomantic-Ui Card view.']);

// *** Simple Card **/

\atk4\ui\Header::addTo($app, ['Card can be defined manually.', 'size' => 3]);

$card = \atk4\ui\Card::addTo($app);

$card->addContent((new \atk4\ui\Header(['Meet Kristy', 'subHeader' => 'Friends'])));

$card->addDescription('Kristy is a friend of Mully.');
$card->addImage('../images/kristy.png');

$card->addButton(new \atk4\ui\Button(['Join']));
$card->addButton(new \atk4\ui\Button(['Email']));

$card->addExtraContent(new \atk4\ui\View(['Copyright notice: Image from Semantic-UI (Fomantic-UI)', 'element' => 'span']));

// *** Simple Card **/

$card = \atk4\ui\Card::addTo($app);
$content = new \atk4\ui\View(['class' => ['content']]);
$content->add($img = new \atk4\ui\Image(['../images/kristy.png']));
$img->addClass('right floated mini ui image');
$content->add($header = new \atk4\ui\Header(['Kristy']));

$card->addContent($content);
$card->addDescription('Friend of Bob');

// **** Card with Table and Label***/

\atk4\ui\Header::addTo($app, ['Card can display model label in a table or in line.', 'size' => 3]);

$deck = \atk4\ui\View::addTo($app, ['ui' => 'cards']);

$cardStat = \atk4\ui\Card::addTo($deck, ['useTable' => true]);
$cardStat->addContent(new \atk4\ui\Header(['Project Info']));
$stats = (new Stat($app->db))->tryLoadAny();

$cardStat->setModel($stats, ['project_name', 'project_code', 'client_name', 'start_date']);

$btn = $cardStat->addButton(new \atk4\ui\Button(['Email Client']));

$cardStat = \atk4\ui\Card::addTo($deck, ['useLabel' => true]);
$cardStat->addContent(new \atk4\ui\Header(['Project Info']));
$stats = (new Stat($app->db))->tryLoadAny();

$cardStat->setModel($stats, ['project_name', 'project_code', 'client_name', 'start_date']);

$cardStat->addButton(new \atk4\ui\Button(['Email Client']));

// **** Card display horizontally ***/

\atk4\ui\Header::addTo($app, ['Card can be display horizontally and/or centered.', 'size' => 3]);

$card = \atk4\ui\Card::addTo($app)->addClass('horizontal centered');

$card->addContent((new \atk4\ui\Header(['Meet Kristy', 'subHeader' => 'Friends'])));
$card->addDescription('Kristy is a friend of Mully.');
$card->addImage('../images/kristy.png');
