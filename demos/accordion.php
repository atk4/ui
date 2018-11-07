<?php

require 'init.php';

$acc = $app->add(['Accordion', 'type' => ['styled', 'fluid']]);

$i = $acc->addItem('Bravo');
$i->add(['Text', 'slfjlksf slkjf lskjf']);

$i = $acc->addItem('Alpha', function ($v) {
    $v->add(['Message', 'Every time you open this accordion item, you will see a different text']);
    $v->add(['LoremIpsum', 'size' => 2]);
});
