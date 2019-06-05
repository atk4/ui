<?php

require 'init.php';
require 'database.php';

//*** Simple Card **/

$card = $app->add('CardHolder');

$card->addContent((new \atk4\ui\Header(['Meet Kristy', 'subHeader' => 'Friends'])));

$card->addDescription('Kristy is a friend of Mully.');

$card->addImage('images/kristy.png');

$card->addButton(new \atk4\ui\Button(['Join']));

$card->addButton(new \atk4\ui\Button(['Email']));

$card->addExtraContent(new \atk4\ui\View(['Copyright notice: Image from Semantic-UI (Fomantic-UI)', 'element' => 'span']));


//**** Card with Data ***/

$card_h = $app->add('CardHolder');
$card_h->addContent(new \atk4\ui\Header(['Project Info']));
$stats = (new Stat($db))->tryLoadAny();

$card_h->setModel($stats, ['project_name', 'project_code', 'client_name']);

$card_h->addButton(new \atk4\ui\Button(['Email Client']));
