<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Form;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

//////////////////////////////////////////////////////////
\atk4\ui\Header::addTo($app, ['Phone', 'size' => 2]);

$f_phone = Form::addTo($app, ['segment']);
\atk4\ui\Label::addTo($f_phone, ['Add other phone field input. Note: phone1 required a number of at least 5 char.', 'top attached'], ['AboveFields']);

$f_phone->addControl('phone1');
$f_phone->addControl('phone2');
$f_phone->addControl('phone3');
$f_phone->addControl('phone4');

// Show phoneX when previous phone is visible and has a number with at least 5 char.
$f_phone->setControlDisplayRules([
    'phone2' => ['phone1' => ['number', 'minLength[5]']],
    'phone3' => ['phone2' => ['number', 'minLength[5]'], 'phone1' => ['number', 'minLength[5]']],
    'phone4' => ['phone3' => ['number', 'minLength[5]'], 'phone2' => ['number', 'minLength[5]'], 'phone1' => ['number', 'minLength[5]']],
]);

//////////////////////////////////////////////////////////
\atk4\ui\Header::addTo($app, ['Optional subscription', 'size' => 2]);

$f_sub = Form::addTo($app, ['segment']);
\atk4\ui\Label::addTo($f_sub, ['Click on subscribe and add email to receive your gift.', 'top attached'], ['AboveFields']);

$f_sub->addControl('name');
$f_sub->addControl('subscribe', [Form\Control\Checkbox::class, 'Subscribe to weekly newsletter', 'toggle']);
$f_sub->addControl('email');
$f_sub->addControl('gender', [Form\Control\Radio::class], ['enum' => ['Female', 'Male']])->set('Female');
$f_sub->addControl('m_gift', [Form\Control\Dropdown::class, 'caption' => 'Gift for Men', 'values' => ['Beer Glass', 'Swiss Knife']]);
$f_sub->addControl('f_gift', [Form\Control\Dropdown::class, 'caption' => 'Gift for Women', 'values' => ['Wine Glass', 'Lipstick']]);

// Show email and gender when subscribe is checked.
// Show m_gift when gender is exactly equal to 'male' and subscribe is checked.
// Show f_gift when gender is exactly equal to 'female' and subscribe is checked.
$f_sub->setControlDisplayRules([
    'email' => ['subscribe' => 'checked'],
    'gender' => ['subscribe' => 'checked'],
    'm_gift' => ['gender' => 'isExactly[Male]', 'subscribe' => 'checked'],
    'f_gift' => ['gender' => 'isExactly[Female]', 'subscribe' => 'checked'],
]);

//////////////////////////////////////////////////////////
\atk4\ui\Header::addTo($app, ['Dog registration', 'size' => 2]);

$f_dog = Form::addTo($app, ['segment']);
\atk4\ui\Label::addTo($f_dog, ['You can select type of hair cut only with race that contains "poodle" AND age no more than 5 year OR your dog race is exactly "bichon".', 'top attached'], ['AboveFields']);
$f_dog->addControl('race', [Form\Control\Line::class]);
$f_dog->addControl('age');
$f_dog->addControl('hair_cut', [Form\Control\Dropdown::class, 'values' => ['Short', 'Long']]);

// Show 'hair_cut' when race contains the word 'poodle' AND age is between 1 and 5
// OR
// Show 'hair_cut' when race contains exactly the word 'bichon'
$f_dog->setControlDisplayRules([
    'hair_cut' => [['race' => 'contains[poodle]', 'age' => 'integer[1..5]'], ['race' => 'isExactly[bichon]']],
]);

//////////////////////////////////////////////////////////
\atk4\ui\Header::addTo($app, ['Hide or show group', 'size' => 2]);

$f_group = Form::addTo($app, ['segment']);
\atk4\ui\Label::addTo($f_group, ['Work on form group too.', 'top attached'], ['AboveFields']);

$g_basic = $f_group->addGroup(['Basic Information']);
$g_basic->addControl('first_name', ['width' => 'eight']);
$g_basic->addControl('middle_name', ['width' => 'three']);
$g_basic->addControl('last_name', ['width' => 'five']);

$f_group->addControl('dev', [Form\Control\Checkbox::class, 'caption' => 'I am a developper']);

$g_code = $f_group->addGroup(['Check all language that apply']);
$g_code->addControl('php', [Form\Control\Checkbox::class]);
$g_code->addControl('js', [Form\Control\Checkbox::class]);
$g_code->addControl('html', [Form\Control\Checkbox::class]);
$g_code->addControl('css', [Form\Control\Checkbox::class]);

$g_other = $f_group->addGroup(['Others']);
$g_other->addControl('language', ['width' => 'eight']);
$g_other->addControl('favorite_pet', ['width' => 'four']);

//To hide-show group simply select a field in that group.
// Show group where 'php' belong when dev is checked.
// Show group where 'language' belong when dev is checked.
$f_group->setGroupDisplayRules(['php' => ['dev' => 'checked'], 'language' => ['dev' => 'checked']]);

//////////////////////////////////////////////////////////
/*
\atk4\ui\Header::addTo($app, ['Hide or show accordion section', 'size'=>2]);

$f_acc = Form::addTo($app, ['segment']);
\atk4\ui\Label::addTo($f_acc, ['Work on section layouts too.', 'top attached'], ['AboveFields']);

// Accordion
$accordion_layout = $f_acc->layout->addSubLayout([Form\Layout\Section\Accordion::class, 'type' => ['styled', 'fluid'], 'settings' => ['exclusive' => false]]);

// Section - business address
$adr_section = $accordion_layout->addSection('Business Address');

$gr = $adr_section->addGroup('Street and City');
$gr->addControl('addr1', ['width' => 'eight'], ['required'=>true]);
$gr->addControl('city1', ['width' => 'eight']);

$gr = $adr_section->addGroup('State, Country and Postal Code');
$gr->addControl('state1', ['width' => 'six']);
$gr->addControl('country1', ['width' => 'six']);
$gr->addControl('postal1', ['width' => 'four']);

$adr_section->addControl('custom_shipping', [Form\Control\Checkbox::class, 'caption'=>'Different Shipping Address']);

// Section - shipping address
$ship_section = $accordion_layout->addSection('Shipping address');

$gr = $ship_section->addGroup('Street and City');
$gr->addControl('addr2', ['width' => 'eight'], ['required'=>true]);
$gr->addControl('city2', ['width' => 'eight']);

$gr = $ship_section->addGroup('State, Country and Postal Code');
$gr->addControl('state2', ['width' => 'six']);
$gr->addControl('country2', ['width' => 'six']);
$gr->addControl('postal2', ['width' => 'four']);

// activate #1 section
$accordion_layout->activate($adr_section);

//To hide-show group or section simply select a field in that group.
// Show group where 'php' belong when dev is checked.
// Show group where 'language' belong when dev is checked.
$f_acc->setGroupDisplayRules(
    // rules
    ['addr2' => ['custom_shipping' => 'checked']]

    // JS selector of container
    //,'.atk-form-group'     // this will hide group
    //,'.content'            // this will hide content of 2nd accordion section
    , $ship_section->owner    // this way we set selector to accordion section title block - so what? we still can't do anything about it
    //                       // BUT there is no way how to show/hide all accordion section including title and content
);
*/
