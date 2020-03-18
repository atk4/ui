<?php

require_once __DIR__ . '/init.php';

$app->add(['Button', 'Nested accordions', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['accordion-nested']);
$app->add(['View', 'ui' => 'clearing divider']);

$app->add(['Header', 'Accordion\'s section can be control programmatically.']);

// toggle menu
$bar = $app->add(['View', 'ui' => 'buttons']);
$b1 = $bar->add(['Button', 'Toggle Section #1']);
$b2 = $bar->add(['Button', 'Toggle Section #2']);
$b3 = $bar->add(['Button', 'Toggle Section #3']);

$app->add(['Header', 'Accordion Sections']);

$accordion = $app->add(['Accordion', 'type' => ['styled', 'fluid']/*, 'settings'=>['exclusive'=>false]*/]);

// static section
$i1 = $accordion->addSection('Static Text');
$i1->add(['Message', 'This content is added on page loaded', 'ui' => 'tiny message']);
$i1->add(['LoremIpsum', 'size' => 1]);

// dynamic section - simple view
$i2 = $accordion->addSection('Dynamic Text', function ($v) {
    $v->add(['Message', 'Every time you open this accordion item, you will see a different text', 'ui' => 'tiny message']);
    $v->add(['LoremIpsum', 'size' => 2]);
});

// dynamic section - form view
$i3 = $accordion->addSection('Dynamic Form', function ($v) {
    $v->add(['Message', 'Loading a form dynamically.', 'ui' => 'tiny message']);
    $f = $v->add(['Form']);
    $f->addField('Email');
    $f->onSubmit(function ($form) {
        return $form->success('Subscribed '.$form->model['Email'].' to newsletter.');
    });
});

// Activate on page load.
$accordion->activate($i2);

$b1->on('click', $accordion->jsToggle($i1));
$b2->on('click', $accordion->jsToggle($i2));
$b3->on('click', $accordion->jsToggle($i3));
