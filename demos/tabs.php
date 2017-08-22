<?php
/**
 * Demonstrates how to use tabs.
 */
require 'init.php';

$t = $layout->add('Tabs');

// static tab
$t->addTab('Hello')->add('HelloWorld');
$t->addTab('Static Tab')->add('LoremIpsum');

// dynamic tab
$t->addTab('Dynamic Lorem Ipsum', function ($tab) {
    $tab->add(['LoremIpsum', 'size'=>2]);
});

// dynamic tab
$t->addTab('Dynamic Form', function ($tab) {
    $m_register = new \atk4\data\Model(new \atk4\data\Persistence_Array($a));
    $m_register->addField('name', ['caption'=>'Please enter your name (John)']);

    $f = $tab->add(new \atk4\ui\Form(['segment'=>true]));
    $f->setModel($m_register);
    $f->onSubmit(function ($f) {
        if ( $f->model['name'] != 'John' ) {
            return $f->error( 'name', 'Your name is not John! It is "' . $f->model['name'] . '". It should be John. Pleeease!' );
        }
    });
});
