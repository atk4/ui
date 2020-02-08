<?php

require_once __DIR__ . '/init.php';

$app->add(['Button', 'Form Sections', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['form-section']);
$app->add(['View', 'ui' => 'ui clearing divider']);

$f = $app->add('Form');

$sub_layout = $f->layout->addSubLayout('Generic');
$sub_layout->add(['Header', 'Please fill all form sections!', 'size' => 4]);

$sub_layout->addField('company_name');

// Accordion
$accordion_layout = $f->layout->addSubLayout(['Accordion', 'type' => ['styled', 'fluid'], 'settings' => ['exclusive' => false]]);

// Section #1
$contact_section = $accordion_layout->addSection('Contact');

$gr = $contact_section->addGroup('Name');
$gr->addField('first_name', ['width' => 'eight'], ['required'=>true]);
$gr->addField('last_name', ['width' => 'eight']);

$gr = $contact_section->addGroup('Email');
$gr->addField('email', ['width' => 'sixteen'], ['caption' => 'yourEmail@domain.com']);

// Section #2
$adr_section = $accordion_layout->addSection('Address');

$gr = $adr_section->addGroup('Street and City');
$gr->addField('address1', ['width' => 'eight'], ['required'=>true]);
$gr->addField('city', ['width' => 'eight']);

$gr = $adr_section->addGroup('State, Country and Postal Code');
$gr->addField('state', ['width' => 'six']);
$gr->addField('country', ['width' => 'six']);
$gr->addField('postal', ['width' => 'four']);

// Sub-Accordion
$sub_accordion_layout = $adr_section->addSubLayout(['Accordion', 'type'=>['styled', 'fluid'], 'settings' => ['exclusive' => false]]);

// Sub-Section #1
$section_1 = $sub_accordion_layout->addSection('Business address');
$section_1->addField('business_address');

// Sub-Section #2
$section_2 = $sub_accordion_layout->addSection('Delivery address');
$section_2->addField('delivery_address', []);

// Terms field
$f->addField('term', ['CheckBox', 'caption'=>'Accept terms and conditions', null, 'slider']);

$accordion_layout->activate($contact_section);

$f->onSubmit(function ($form) {
    return $form->success('Yey!', 'You did well by filling out this form');
});
