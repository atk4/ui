<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Button::addTo($app, ['Nested accordions', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['accordion-nested']);
\Atk4\Ui\View::addTo($app, ['ui' => 'clearing divider']);

\Atk4\Ui\Header::addTo($app, ['Accordion\'s section can be control programmatically.']);

// toggle menu
$bar = \Atk4\Ui\View::addTo($app, ['ui' => 'buttons']);
$b1 = \Atk4\Ui\Button::addTo($bar, ['Toggle Section #1']);
$b2 = \Atk4\Ui\Button::addTo($bar, ['Toggle Section #2']);
$b3 = \Atk4\Ui\Button::addTo($bar, ['Toggle Section #3']);

\Atk4\Ui\Header::addTo($app, ['Accordion Sections']);

$accordion = \Atk4\Ui\Accordion::addTo($app, ['type' => ['styled', 'fluid']/*, 'settings'=>['exclusive'=>false]*/]);

// static section
$i1 = $accordion->addSection('Static Text');
\Atk4\Ui\Message::addTo($i1, ['This content is added on page loaded', 'ui' => 'tiny message']);
\Atk4\Ui\LoremIpsum::addTo($i1, ['size' => 1]);

// dynamic section - simple view
$i2 = $accordion->addSection('Dynamic Text', function ($v) {
    \Atk4\Ui\Message::addTo($v, ['Every time you open this accordion item, you will see a different text', 'ui' => 'tiny message']);
    \Atk4\Ui\LoremIpsum::addTo($v, ['size' => 2]);
});

// dynamic section - form view
$i3 = $accordion->addSection('Dynamic Form', function ($v) {
    \Atk4\Ui\Message::addTo($v, ['Loading a form dynamically.', 'ui' => 'tiny message']);
    $form = Form::addTo($v);
    $form->addControl('Email');
    $form->onSubmit(function (Form $form) {
        return $form->success('Subscribed ' . $form->model->get('Email') . ' to newsletter.');
    });
});

// Activate on page load.
$accordion->activate($i2);

$b1->on('click', $accordion->jsToggle($i1));
$b2->on('click', $accordion->jsToggle($i2));
$b3->on('click', $accordion->jsToggle($i3));
