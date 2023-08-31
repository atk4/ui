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

$addAccordionFx = static function ($view, int $maxDepth, int $level = 0) use (&$addAccordionFx) {
    $accordion = Accordion::addTo($view, ['type' => ['styled', 'fluid']]);

    // static section
    $i1 = $accordion->addSection('Static Text');
    Message::addTo($i1, ['This content is added on page loaded', 'ui' => 'tiny message']);
    LoremIpsum::addTo($i1, ['size' => 1]);
    if ($level < $maxDepth) {
        $addAccordionFx($i1, $maxDepth, $level + 1);
    }

    // dynamic section - simple view
    $i2 = $accordion->addSection('Dynamic Text', static function (VirtualPage $vp) use ($addAccordionFx, $maxDepth, $level) {
        Message::addTo($vp, ['Every time you open this accordion item, you will see a different text', 'ui' => 'tiny message']);
        LoremIpsum::addTo($vp, ['size' => 2]);

        $addAccordionFx($vp, $maxDepth, $level + 1);
    });

    // dynamic section - form view
    $i3 = $accordion->addSection('Dynamic Form', static function (VirtualPage $vp) use ($addAccordionFx, $maxDepth, $level) {
        Message::addTo($vp, ['Loading a form dynamically.', 'ui' => 'tiny message']);
        $form = Form::addTo($vp);
        $form->addControl('email');
        $form->onSubmit(static function (Form $form) {
            return $form->jsSuccess('Subscribed ' . $form->model->get('email') . ' to newsletter.');
        });

        $addAccordionFx($vp, $maxDepth, $level + 1);
    });

    return $accordion;
};

// add accordion structure
$addAccordionFx($app, 4);
