<?php

require 'init.php';

$app->add(['Button', 'Form Sections', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['form-section']);
$app->add(['View', 'ui' => 'ui clearing divider']);

$f = $app->add('Form');

$sub_layout = $f->layout->addSubLayout('Generic');
$sub_layout->add(['Header', 'Please fill all form sections!', 'size' => 4]);

$sub_layout->addField('company_name');

$accordion_layout = $f->layout->addSubLayout(['Accordion', 'settings' => ['exclusive' => false]]);

$contact_section = $accordion_layout->addSection('Contact');

$gr = $contact_section->addGroup('Name');
$gr->addField('first_name', ['width' => 'eight'], ['required'=>true]);
$gr->addField('last_name', ['width' => 'eight']);

$gr = $contact_section->addGroup('Email');
$gr->addField('email', ['width' => 'sixteen'], ['caption' => 'yourEmail@domain.com']);

$adr_section = $accordion_layout->addSection('Address');

$gr = $adr_section->addGroup('Street and City');
$gr->addField('address1', ['width' => 'eight'], ['required'=>true]); // <-- this is cought first and accordion section don't expand
$gr->addField('city', ['width' => 'eight']);

$gr = $adr_section->addGroup('State, Country and Postal Code');
$gr->addField('state', ['width' => 'six']);
$gr->addField('country', ['width' => 'six']);
$gr->addField('postal', ['width' => 'four']);

$f->addField('term', ['CheckBox', 'caption'=>'Accept terms and conditions', null, 'slider']);

$accordion_layout->activate($contact_section);

$f->onSubmit(function($form){
    return $form->success('Yey!', 'You did well by filling out this form');
});
