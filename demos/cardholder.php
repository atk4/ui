<?php

require 'init.php';
require 'database.php';

//*** Simple Card **/
$app->add(['Header', 'Card can be defined manually.', 'size' => 3]);

$card = $app->add('CardHolder');

$card->addContent((new \atk4\ui\Header(['Meet Kristy', 'subHeader' => 'Friends'])));
$card->addDescription('Kristy is a friend of Mully.');
$card->addImage('images/kristy.png');

$card->addButton(new \atk4\ui\Button(['Join']));
$card->addButton(new \atk4\ui\Button(['Email']));

$card->addExtraContent(new \atk4\ui\View(['Copyright notice: Image from Semantic-UI (Fomantic-UI)', 'element' => 'span']));

// Card with model ** /
$app->add(['Header', 'Card can display model content.', 'size' => 3]);

$country = new Country($db);

// in model
$country->addExpression('extra', 'concat("iso: ", [iso])');
$country->addCalculatedField('desc', function ($m) {
    $name = $m->getTitle();
    $number = number_format(rand(1000000, 10000000));
    return "The country of " . $name . ' has more than ' . $number . ' habitants';
});

$country->setLimit(6);

// Card Deck //
$deck = $app->add(['ui' => 'cards']);

foreach ($country as $m) {
    $c = $deck->add('CardHolder');
    $c->setModel($m, ['desc'], ['extra']);
}

//**** Card with Table ***/
$app->add(['Header', 'Card can display model content in a table.', 'size' => 3]);


$card_s = $app->add(['CardHolder', 'useTable' => true]);
$card_s->addContent(new \atk4\ui\Header(['Project Info']));
$stats = (new Stat($db))->tryLoadAny();

$card_s->setModel($stats, ['project_name', 'project_code', 'client_name']);

$card_s->addButton(new \atk4\ui\Button(['Email Client']));
