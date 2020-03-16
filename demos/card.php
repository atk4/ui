<?php

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

$app->add(['Button', 'Card Model', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['card-action']);
$app->add(['View', 'ui' => 'ui clearing divider']);

$app->add(['Header', 'Card.', 'size' => 1, 'subHeader' => 'Component based on Fomantic-Ui Card view.']);

//*** Simple Card **/

$app->add(['Header', 'Card can be defined manually.', 'size' => 3]);

$card = $app->add('Card');

$card->addContent((new \atk4\ui\Header(['Meet Kristy', 'subHeader' => 'Friends'])));

$card->addDescription('Kristy is a friend of Mully.');
$card->addImage('images/kristy.png');

$card->addButton(new \atk4\ui\Button(['Join']));
$card->addButton(new \atk4\ui\Button(['Email']));

$card->addExtraContent(new \atk4\ui\View(['Copyright notice: Image from Semantic-UI (Fomantic-UI)', 'element' => 'span']));

//*** Simple Card **/

$card = $app->add('Card');
$content = new \atk4\ui\View(['class' => ['content']]);
$content->add($img = new \atk4\ui\Image(['images/kristy.png']));
$img->addClass('right floated mini ui image');
$content->add($header = new \atk4\ui\Header(['Kristy']));

$card->addContent($content);
$card->addDescription('Friend of Bob');

//**** Card with Table and Label***/

$app->add(['Header', 'Card can display model label in a table or in line.', 'size' => 3]);

$deck = $app->add(['View', 'ui' => 'cards']);

$card_s = $deck->add(['Card', 'useTable' => true]);
$card_s->addContent(new \atk4\ui\Header(['Project Info']));
$stats = (new Stat($db))->tryLoadAny();

$card_s->setModel($stats, ['project_name', 'project_code', 'client_name', 'start_date']);

$btn = $card_s->addButton(new \atk4\ui\Button(['Email Client']));

$card_s = $deck->add(['Card', 'useLabel' => true]);
$card_s->addContent(new \atk4\ui\Header(['Project Info']));
$stats = (new Stat($db))->tryLoadAny();

$card_s->setModel($stats, ['project_name', 'project_code', 'client_name', 'start_date']);

$card_s->addButton(new \atk4\ui\Button(['Email Client']));

//**** Card display horizontally ***/

$app->add(['Header', 'Card can be display horizontally and/or centered.', 'size' => 3]);

$card = $app->add('Card')->addClass('horizontal centered');

$card->addContent((new \atk4\ui\Header(['Meet Kristy', 'subHeader' => 'Friends'])));
$card->addDescription('Kristy is a friend of Mully.');
$card->addImage('images/kristy.png');
