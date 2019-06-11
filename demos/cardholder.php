<?php

require 'init.php';
require 'database.php';

$app->add(['Button', 'CardHolder actions', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['cardholder-action']);
$app->add(['View', 'ui' => 'ui clearing divider']);

$app->add(['Header', 'CardHolder.', 'size' => 1, 'subHeader' => 'Component based on Fomantic-Ui Card view.']);

//*** Simple Card **/
$app->add(['Header', 'CardHolder can be defined manually.', 'size' => 3]);

$card = $app->add('CardHolder');

$card->addContent((new \atk4\ui\Header(['Meet Kristy', 'subHeader' => 'Friends'])));
$card->addDescription('Kristy is a friend of Mully.');
$card->addImage('images/kristy.png');

$card->addButton(new \atk4\ui\Button(['Join']));
$card->addButton(new \atk4\ui\Button(['Email']));

$card->addExtraContent(new \atk4\ui\View(['Copyright notice: Image from Semantic-UI (Fomantic-UI)', 'element' => 'span']));

//*** Simple Card **/
$card = $app->add('CardHolder');
$content = new \atk4\ui\View(['class' => ['content']]);
$content->add($img = new \atk4\ui\Image(['images/kristy.png']));
$img->addClass('right floated mini ui image');
$content->add($header = new \atk4\ui\Header(['Kristy']));

$card->addContent($content);
$card->addDescription('Friend of Bob');

// Card with model ** /
$app->add(['Header', 'CardHolder can display model content.', 'size' => 3]);

$country = new Country($db);
$country->addExpression('extra', 'concat("iso: ", [iso])');
$country->addCalculatedField('desc', function ($m) {
    $name = $m->getTitle();
    $number = number_format(rand(1000000, 10000000));

    return 'The country of '.$name.' has more than '.$number.' habitants';
});
$country->getField('desc')->type = 'money';

// Card Deck //
$deck = $app->add(['ui' => 'cards']);

$country->setLimit(8);
$country->each(function ($m) use ($deck) {
    $c = $deck->add('CardHolder');
    $c->setModel($m, ['iso', 'desc'], ['extra']);
});

//**** Card with Table ***/
$app->add(['Header', 'CardHolder can display model label in a table or in line.', 'size' => 3]);

$deck = $app->add(['ui' => 'cards']);

$card_s = $deck->add(['CardHolder', 'useTable' => true]);
$card_s->addContent(new \atk4\ui\Header(['Project Info']));
$stats = (new Stat($db))->tryLoadAny();

$card_s->setModel($stats, ['project_name', 'project_code', 'client_name']);

$btn = $card_s->addButton(new \atk4\ui\Button(['Email Client']));

$card_s = $deck->add(['CardHolder', 'useLabel' => true]);
$card_s->addContent(new \atk4\ui\Header(['Project Info']));
$stats = (new Stat($db))->tryLoadAny();

$card_s->setModel($stats, ['project_name', 'project_code', 'client_name']);

$card_s->addButton(new \atk4\ui\Button(['Email Client']));

//**** Card display horizontally ***/
$app->add(['Header', 'CardHolder can be display horizontally and/or centered.', 'size' => 3]);

$card = $app->add('CardHolder')->addClass('horizontal centered');

$card->addContent((new \atk4\ui\Header(['Meet Kristy', 'subHeader' => 'Friends'])));
$card->addDescription('Kristy is a friend of Mully.');
$card->addImage('images/kristy.png');
