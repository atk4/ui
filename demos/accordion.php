<?php

require 'init.php';

$acc = $app->add(['Accordion', 'type' => ['styled','fluid']]);

$i = $acc->addItem('I am a static Item');
$i->add(['Message', 'This content is added on page loaded']);
$i->add(['LoremIpsum', 'size' => 1]);

$i = $acc->addItem('I am a dynamic Item', function($v){
    $v->add(['Message', 'Every time you open this accordion item, you will see a different text']);
    $v->add(['LoremIpsum', 'size' => 2]);
});