<?php

require_once __DIR__ . '/../atk-init.php';

\atk4\ui\Button::addTo($app, ['Nested accordions', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['accordion-nested']);
\atk4\ui\View::addTo($app, ['ui' => 'clearing divider']);

\atk4\ui\Header::addTo($app, ['Accordion\'s section can be control programmatically.']);

// toggle menu
$bar = \atk4\ui\View::addTo($app, ['ui' => 'buttons']);
$b1 = \atk4\ui\Button::addTo($bar, ['Toggle Section #1']);
$b2 = \atk4\ui\Button::addTo($bar, ['Toggle Section #2']);
$b3 = \atk4\ui\Button::addTo($bar, ['Toggle Section #3']);

\atk4\ui\Header::addTo($app, ['Accordion Sections']);

$accordion = \atk4\ui\Accordion::addTo($app, ['type' => ['styled', 'fluid']/*, 'settings'=>['exclusive'=>false]*/]);

// static section
$i1 = $accordion->addSection('Static Text');
\atk4\ui\Message::addTo($i1, ['This content is added on page loaded', 'ui' => 'tiny message']);
\atk4\ui\LoremIpsum::addTo($i1, ['size' => 1]);

// dynamic section - simple view
$i2 = $accordion->addSection('Dynamic Text', function ($v) {
    \atk4\ui\Message::addTo($v, ['Every time you open this accordion item, you will see a different text', 'ui' => 'tiny message']);
    \atk4\ui\LoremIpsum::addTo($v, ['size' => 2]);
});

// dynamic section - form view
$i3 = $accordion->addSection('Dynamic Form', function ($v) {
    \atk4\ui\Message::addTo($v, ['Loading a form dynamically.', 'ui' => 'tiny message']);
    $f = \atk4\ui\Form::addTo($v);
    $f->addField('Email');
    $f->onSubmit(function ($form) {
        return $form->success('Subscribed ' . $form->model->get('Email') . ' to newsletter.');
    });
});

// Activate on page load.
$accordion->activate($i2);

$b1->on('click', $accordion->jsToggle($i1));
$b2->on('click', $accordion->jsToggle($i2));
$b3->on('click', $accordion->jsToggle($i3));
