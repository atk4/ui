<?php
/**
 * Demonstrates how to use tabs.
 */
require 'init.php';

$t = $app->add('Tabs');

// static tab
$t->addTab('Hello')->add('HelloWorld');
$tab = $t->addTab('Static Tab');
$tab->add(['Message', 'Content of this tab will refresh only if you reload entire page']);
$tab->add('LoremIpsum');

// dynamic tab
$t->addTab('Dynamic Lorem Ipsum', function ($tab) {
    $tab->add(['Message', 'Every time you come to this tab, you will see a different text']);
    $tab->add(['LoremIpsum', 'size' => 2]);
});

// dynamic tab
$t->addTab('Dynamic Form', function ($tab) {
    $tab->add(['Message', 'It takes 2 seconds for this tab to load', 'warning']);
    sleep(2);
    $m_register = new \atk4\data\Model(new \atk4\data\Persistence_Array($a));
    $m_register->addField('name', ['caption' => 'Please enter your name (John)']);

    $f = $tab->add(new \atk4\ui\Form(['segment' => true]));
    $f->setModel($m_register);
    $f->onSubmit(function ($f) {
        if ($f->model['name'] != 'John') {
            return $f->error('name', 'Your name is not John! It is "'.$f->model['name'].'". It should be John. Pleeease!');
        }
    });
});

$t->addTabURL('Any other page', $app->url(['index']));
