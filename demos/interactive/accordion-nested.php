<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Accordion;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\LoremIpsum;
use Atk4\Ui\Message;
use Atk4\Ui\VirtualPage;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Nested accordions']);

$addAccordionFunc = function ($view, int $maxDepth, int $level = 0) use (&$addAccordionFunc) {
    $accordion = Accordion::addTo($view, ['type' => ['styled', 'fluid']]);

    // static section
    $i1 = $accordion->addSection('Static Text');
    Message::addTo($i1, ['This content is added on page loaded', 'ui' => 'tiny message']);
    LoremIpsum::addTo($i1, ['size' => 1]);
    if ($level < $maxDepth) {
        $addAccordionFunc($i1, $maxDepth, $level + 1);
    }

    // dynamic section - simple view
    $i2 = $accordion->addSection('Dynamic Text', function (VirtualPage $vp) use ($addAccordionFunc, $maxDepth, $level) {
        Message::addTo($vp, ['Every time you open this accordion item, you will see a different text', 'ui' => 'tiny message']);
        LoremIpsum::addTo($vp, ['size' => 2]);

        $addAccordionFunc($vp, $maxDepth, $level + 1);
    });

    // dynamic section - form view
    $i3 = $accordion->addSection('Dynamic Form', function (VirtualPage $vp) use ($addAccordionFunc, $maxDepth, $level) {
        Message::addTo($vp, ['Loading a form dynamically.', 'ui' => 'tiny message']);
        $form = Form::addTo($vp);
        $form->addControl('email');
        $form->onSubmit(function (Form $form) {
            return $form->success('Subscribed ' . $form->model->get('email') . ' to newsletter.');
        });

        $addAccordionFunc($vp, $maxDepth, $level + 1);
    });

    return $accordion;
};

// add accordion structure
$addAccordionFunc($app, 4);
