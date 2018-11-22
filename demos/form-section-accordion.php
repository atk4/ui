<?php

require 'init.php';

$app->add(['Button', 'Form Sections', 'small left floated basic blue', 'icon' => 'left arrow'])
    ->link(['scroll-grid']);
$app->add(['View', 'ui' => 'ui clearing divider']);


$f = $app->add('Form');

$v = $f->layout->addLayout();
$v->add(['Header', 'Please fill all form sections!', 'size' => 4]);

$v->addField('company_name');

$acc = $f->layout->addLayout('Accordion');

$contact_section = $acc->addSection('Contact');

$gr = $contact_section->addGroup('Name');
$gr->addField('first_name', ['width' => 'eight']);
$gr->addField('last_name', ['width' => 'eight']);

$gr = $contact_section->addGroup('Email');
$gr->addField('email', ['width' => 'sixteen'], ['caption' => 'yourEmail@domain.com']);

$adr_section = $acc->addSection('Address');

$gr = $adr_section->addGroup('Street and City');
$gr->addField('address1', ['width' => 'eight'], ['required'=>true]); // <-- this is cought first and accordion section don't expand
$gr->addField('city', ['width' => 'eight']);

$gr = $adr_section->addGroup('State, Country and Postal Code');
$gr->addField('state', ['width' => 'six']);
$gr->addField('country', ['width' => 'six']);
$gr->addField('postal', ['width' => 'four']);

$f->addField('term', ['CheckBox', 'caption'=>'Accept terms and conditions', null, 'slider']);

$acc->activate($contact_section);

$f->onSubmit(function ($f) use ($acc, $contact_section) {
    if (!$f->model['first_name']) {
        // return field error and open proper accordion item where field is located.
        return [
            $f->error('first_name', 'Your first name is required.'),
            $acc->jsOpen($contact_section),
        ];
    }
});
