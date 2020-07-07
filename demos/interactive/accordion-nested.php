<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

/*
\atk4\ui\Button::addTo($app, ['View Form input split in Accordion section', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['accordion-in-form']);
\atk4\ui\View::addTo($app, ['ui' => 'clearing divider']);
*/

\atk4\ui\Header::addTo($app, ['Nested accordions']);

$addAccordionFunc = function ($view, $maxDepth = 2, $level = 0) use (&$addAccordionFunc) {
    $accordion = \atk4\ui\Accordion::addTo($view, ['type' => ['styled', 'fluid']]);

    // static section
    $i1 = $accordion->addSection('Static Text');
    \atk4\ui\Message::addTo($i1, ['This content is added on page loaded', 'ui' => 'tiny message']);
    \atk4\ui\LoremIpsum::addTo($i1, ['size' => 1]);
    if ($level < $maxDepth) {
        $addAccordionFunc($i1, $maxDepth, $level + 1);
    }

    // dynamic section - simple view
    $i2 = $accordion->addSection('Dynamic Text', function ($v) use ($maxDepth, $level) {
        \atk4\ui\Message::addTo($v, ['Every time you open this accordion item, you will see a different text', 'ui' => 'tiny message']);
        \atk4\ui\LoremIpsum::addTo($v, ['size' => 2]);
        if ($level < $maxDepth) {
            $addAccordionFunc($v, $maxDepth, $level + 1);
        }
    });

    // dynamic section - form view
    $i3 = $accordion->addSection('Dynamic Form', function ($v) use ($maxDepth, $level) {
        \atk4\ui\Message::addTo($v, ['Loading a form dynamically.', 'ui' => 'tiny message']);
        $form = \atk4\ui\Form::addTo($v);
        $form->addControl('Email');
        $form->onSubmit(function (\atk4\ui\Form $form) {
            return $form->success('Subscribed ' . $form->model->get('Email') . ' to newsletter.');
        });

        if ($level < $maxDepth) {
            $addAccordionFunc($v, $maxDepth, $level + 1);
        }
    });
};

// add accordion structure
$a = $addAccordionFunc($app);
