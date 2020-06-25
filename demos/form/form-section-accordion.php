<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Form;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

\atk4\ui\Button::addTo($app, ['Form Sections', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['form-section']);
\atk4\ui\View::addTo($app, ['ui' => 'ui clearing divider']);

$form = Form::addTo($app);

$sub_layout = $form->layout->addSubLayout(Form\Layout\Section::class);
\atk4\ui\Header::addTo($sub_layout, ['Please fill all form sections!', 'size' => 4]);

$sub_layout->addField('company_name');

// Accordion
$accordion_layout = $form->layout->addSubLayout([Form\Layout\Section\Accordion::class, 'type' => ['styled', 'fluid'], 'settings' => ['exclusive' => false]]);

// Section #1
$contact_section = $accordion_layout->addSection('Contact');

$group = $contact_section->addGroup('Name');
$group->addField('first_name', ['width' => 'eight'], ['required' => true]);
$group->addField('last_name', ['width' => 'eight']);

$group = $contact_section->addGroup('Email');
$group->addField('email', ['width' => 'sixteen'], ['caption' => 'yourEmail@domain.com']);

// Section #2
$adr_section = $accordion_layout->addSection('Address');

$group = $adr_section->addGroup('Street and City');
$group->addField('address1', ['width' => 'eight'], ['required' => true]);
$group->addField('city', ['width' => 'eight']);

$group = $adr_section->addGroup('State, Country and Postal Code');
$group->addField('state', ['width' => 'six']);
$group->addField('country', ['width' => 'six']);
$group->addField('postal', ['width' => 'four']);

// Sub-Accordion
$sub_accordion_layout = $adr_section->addSubLayout([Form\Layout\Section\Accordion::class, 'type' => ['styled', 'fluid'], 'settings' => ['exclusive' => false]]);

// Sub-Section #1
$section_1 = $sub_accordion_layout->addSection('Business address');
$section_1->addField('business_address');

// Sub-Section #2
$section_2 = $sub_accordion_layout->addSection('Delivery address');
$section_2->addField('delivery_address', []);

// Terms field
$form->addField('term', [Form\Control\Checkbox::class, 'caption' => 'Accept terms and conditions', null, 'slider']);

$accordion_layout->activate($contact_section);

$form->onSubmit(function (Form $form) {
    return $form->success('Yey!', 'You did well by filling out this form');
});
