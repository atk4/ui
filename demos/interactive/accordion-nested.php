<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

/*
\Atk4\Ui\Button::addTo($app, ['View Form input split in Accordion section', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['accordion-in-form']);
\Atk4\Ui\View::addTo($app, ['ui' => 'clearing divider']);
*/

\Atk4\Ui\Header::addTo($app, ['Nested accordions']);

$addAccordionFunc = function ($view, $maxDepth = 2, $level = 0) use (&$addAccordionFunc) {
    $accordion = \Atk4\Ui\Accordion::addTo($view, ['type' => ['styled', 'fluid']]);

    // static section
    $i1 = $accordion->addSection('Static Text');
    \Atk4\Ui\Message::addTo($i1, ['This content is added on page loaded', 'ui' => 'tiny message']);
    \Atk4\Ui\LoremIpsum::addTo($i1, ['size' => 1]);
    if ($level < $maxDepth) {
        $addAccordionFunc($i1, $maxDepth, $level + 1);
    }

    // dynamic section - simple view
    $i2 = $accordion->addSection('Dynamic Text', function ($v) use ($addAccordionFunc, $maxDepth, $level) {
        \Atk4\Ui\Message::addTo($v, ['Every time you open this accordion item, you will see a different text', 'ui' => 'tiny message']);
        \Atk4\Ui\LoremIpsum::addTo($v, ['size' => 2]);
        if ($level < $maxDepth) {
            $addAccordionFunc($v, $maxDepth, $level + 1);
        }
    });

    // dynamic section - form view
    $i3 = $accordion->addSection('Dynamic Form', function ($v) use ($addAccordionFunc, $maxDepth, $level) {
        \Atk4\Ui\Message::addTo($v, ['Loading a form dynamically.', 'ui' => 'tiny message']);
        $form = \Atk4\Ui\Form::addTo($v);
        $form->addControl('Email');
        $form->onSubmit(function (\Atk4\Ui\Form $form) {
            return $form->success('Subscribed ' . $form->model->get('Email') . ' to newsletter.');
        });

        if ($level < $maxDepth) {
            $addAccordionFunc($v, $maxDepth, $level + 1);
        }
    });
};

// add accordion structure
$a = $addAccordionFunc($app);
