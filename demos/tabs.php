<?php
/**
 * Demonstrates how to use tabs.
 */
require 'init.php';

$t = $layout->add('Tabs');

// static tab
$t->addTab('Hello')->add('HelloWorld');
$t->addTab('Lorem')->add('LoremIpsum');

// dynamic tab
$t->addTab('Dynamic', function($tab){
    sleep(3);
    $tab->add('LoremIpsum');
});
