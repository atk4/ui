<?php

require 'init.php';

$f = $app->add(['Form', 'layout' => 'Accordion']);

$contact_section = $f->layout->addSection('Contact');

$gr = $contact_section->addGroup('Name');
$gr->addField('first_name', ['width' => 'eight']);
$gr->addField('last_name', ['width' => 'eight']);

$gr = $contact_section->addGroup('Email');
$gr->addField('email', ['width' => 'sixteen'], ['caption' => 'yourEmail@domain.com']);

$adr_section = $f->layout->addSection('Address');

$gr = $adr_section->addGroup('Street and City');
$gr->addField('address1', ['width' => 'eight']);
$gr->addField('city', ['width' => 'eight']);

$gr = $adr_section->addGroup('State, Country and Postal Code');
$gr->addField('state', ['width' => 'six']);
$gr->addField('country', ['width' => 'six']);
$gr->addField('postal', ['width' => 'four']);

$f->addField('term', ['CheckBox', 'caption'=>'Accept terms and conditions', null, 'slider']);

$f->layout->getAccordion()->activate($f->layout->getSection($contact_section));

$f->onSubmit(function ($f) use ($contact_section){
    if (!$f->model['first_name']) {
        // return field error and open proper accordion item where field is located.
        return [
            $f->error('first_name', 'Your first name is required.'),
            $f->layout->getAccordion()->jsOpen($f->layout->getSection($contact_section)),
            ];
    }
});

