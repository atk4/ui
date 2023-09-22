<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Accordion;
use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\LoremIpsum;
use Atk4\Ui\Message;
use Atk4\Ui\View;
use Atk4\Ui\VirtualPage;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Button::addTo($app, ['Nested accordions', 'class.small right floated basic blue' => true, 'iconRight' => 'right arrow'])
    ->link(['accordion-nested']);
View::addTo($app, ['ui' => 'clearing divider']);

Header::addTo($app, ['Accordion\'s section can be control programmatically.']);

// toggle menu
$bar = View::addTo($app, ['ui' => 'buttons']);
$b1 = Button::addTo($bar, ['Toggle Section #1']);
$b2 = Button::addTo($bar, ['Toggle Section #2']);
$b3 = Button::addTo($bar, ['Toggle Section #3']);

Header::addTo($app, ['Accordion Sections']);

$accordion = Accordion::addTo($app, ['type' => ['styled', 'fluid']/* , 'settings' => ['exclusive' => false] */]);

// static section
$i1 = $accordion->addSection('Static Text');
Message::addTo($i1, ['This content is added on page loaded', 'ui' => 'tiny message']);
LoremIpsum::addTo($i1, ['size' => 1]);

// dynamic section - simple view
$i2 = $accordion->addSection('Dynamic Text', static function (VirtualPage $vp) {
    Message::addTo($vp, ['Every time you open this accordion item, you will see a different text', 'ui' => 'tiny message']);
    LoremIpsum::addTo($vp, ['size' => 2]);
});

// dynamic section - form view
$i3 = $accordion->addSection('Dynamic Form', static function (VirtualPage $vp) {
    Message::addTo($vp, ['Loading a form dynamically.', 'ui' => 'tiny message']);
    $form = Form::addTo($vp);
    $form->addControl('Email');
    $form->onSubmit(static function (Form $form) {
        return $form->jsSuccess('Subscribed ' . $form->model->get('Email') . ' to newsletter.');
    });
});

// activate on page load
$accordion->activate($i2);

$b1->on('click', $accordion->jsToggle($i1));
$b2->on('click', $accordion->jsToggle($i2));
$b3->on('click', $accordion->jsToggle($i3));
