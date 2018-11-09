<?php

require 'init.php';

$f = $app->add(['Form', 'layout' => 'Flexible']);

$acc = $f->layout->addView(['Accordion', 'type' => ['styled', 'fluid']]);

//Contact field in accordion item 1.
//Start by enabling addField to accordion item view.
$acc_item_1 = $f->layout->addLayoutSection($item_1 = $acc->addItem('Contact'));

$gr = $acc_item_1->addGroup('Name');
$gr->addField('first_name', ['width' => 'eight']);
$gr->addField('last_name', ['width' => 'eight']);
$gr = $acc_item_1->addGroup('Email');
$gr->addField('email', ['width' => 'sixteen'], ['caption' => 'yourEmail@domain.com']);

//Address field in accordion item 2.
//Start by enabling addField to accordion item 2.
$acc_item_2 = $f->layout->addLayoutSection($acc->addItem('Address'));
$gr = $acc_item_2->addGroup('Street and City');
$gr->addField('address1', ['width' => 'eight']);
$gr->addField('city', ['width' => 'eight']);

$gr = $acc_item_2->addGroup('State, Country and Postal Code');
$gr->addField('state', ['width' => 'six']);
$gr->addField('country', ['width' => 'six']);
$gr->addField('postal', ['width' => 'four']);

$f->addField('term', ['CheckBox', 'caption'=>'Accept terms and conditions', null, 'slider']);

$f->onSubmit(function ($f) use ($acc, $item_1) {
    if (!$f->model['first_name']) {
        // return field error and open proper accordion item where field is located.
        return [
            $f->error('first_name', 'Your first name is required.'),
            $acc->jsOpen($item_1),
            ];
    }
});

$acc->activate($item_1);
