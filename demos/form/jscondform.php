<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

//////////////////////////////////////////////////////////
\Atk4\Ui\Header::addTo($app, ['Phone', 'size' => 2]);

$formPhone = Form::addTo($app, ['segment']);
\Atk4\Ui\Label::addTo($formPhone, ['Add other phone field input. Note: phone1 required a number of at least 5 char.', 'top attached'], ['AboveControls']);

$formPhone->addControl('phone1');
$formPhone->addControl('phone2');
$formPhone->addControl('phone3');
$formPhone->addControl('phone4');

// Show phoneX when previous phone is visible and has a number with at least 5 char.
$formPhone->setControlsDisplayRules([
    'phone2' => ['phone1' => ['number', 'minLength[5]']],
    'phone3' => ['phone2' => ['number', 'minLength[5]'], 'phone1' => ['number', 'minLength[5]']],
    'phone4' => ['phone3' => ['number', 'minLength[5]'], 'phone2' => ['number', 'minLength[5]'], 'phone1' => ['number', 'minLength[5]']],
]);

//////////////////////////////////////////////////////////
\Atk4\Ui\Header::addTo($app, ['Optional subscription', 'size' => 2]);

$formSubscribe = Form::addTo($app, ['segment']);
\Atk4\Ui\Label::addTo($formSubscribe, ['Click on subscribe and add email to receive your gift.', 'top attached'], ['AboveControls']);

$formSubscribe->addControl('name');
$formSubscribe->addControl('subscribe', [Form\Control\Checkbox::class, 'Subscribe to weekly newsletter', 'toggle']);
$formSubscribe->addControl('email');
$formSubscribe->addControl('gender', [Form\Control\Radio::class], ['enum' => ['Female', 'Male']])->set('Female');
$formSubscribe->addControl('m_gift', [Form\Control\Dropdown::class, 'caption' => 'Gift for Men', 'values' => ['Beer Glass', 'Swiss Knife']]);
$formSubscribe->addControl('f_gift', [Form\Control\Dropdown::class, 'caption' => 'Gift for Women', 'values' => ['Wine Glass', 'Lipstick']]);

// Show email and gender when subscribe is checked.
// Show m_gift when gender = 'male' and subscribe is checked.
// Show f_gift when gender = 'female' and subscribe is checked.
$formSubscribe->setControlsDisplayRules([
    'email' => ['subscribe' => 'checked'],
    'gender' => ['subscribe' => 'checked'],
    'm_gift' => ['gender' => 'isExactly[Male]', 'subscribe' => 'checked'],
    'f_gift' => ['gender' => 'isExactly[Female]', 'subscribe' => 'checked'],
]);

//////////////////////////////////////////////////////////
\Atk4\Ui\Header::addTo($app, ['Dog registration', 'size' => 2]);

$formDog = Form::addTo($app, ['segment']);
\Atk4\Ui\Label::addTo($formDog, ['You can select type of hair cut only with race that contains "poodle" AND age no more than 5 year OR your dog race equals "bichon".', 'top attached'], ['AboveControls']);
$formDog->addControl('race', [Form\Control\Line::class]);
$formDog->addControl('age');
$formDog->addControl('hair_cut', [Form\Control\Dropdown::class, 'values' => ['Short', 'Long']]);

// Show 'hair_cut' when race contains the word 'poodle' AND age is between 1 and 5
// OR
// Show 'hair_cut' when race contains exactly the word 'bichon'
$formDog->setControlsDisplayRules([
    'hair_cut' => [['race' => 'contains[poodle]', 'age' => 'integer[1..5]'], ['race' => 'isExactly[bichon]']],
]);

//////////////////////////////////////////////////////////
\Atk4\Ui\Header::addTo($app, ['Hide or show group', 'size' => 2]);

$formGroup = Form::addTo($app, ['segment']);
\Atk4\Ui\Label::addTo($formGroup, ['Work on form group too.', 'top attached'], ['AboveControls']);

$groupBasic = $formGroup->addGroup(['Basic Information']);
$groupBasic->addControl('first_name', ['width' => 'eight']);
$groupBasic->addControl('middle_name', ['width' => 'three']);
$groupBasic->addControl('last_name', ['width' => 'five']);

$formGroup->addControl('dev', [Form\Control\Checkbox::class, 'caption' => 'I am a developper']);

$groupCode = $formGroup->addGroup(['Check all language that apply']);
$groupCode->addControl('php', [Form\Control\Checkbox::class]);
$groupCode->addControl('js', [Form\Control\Checkbox::class]);
$groupCode->addControl('html', [Form\Control\Checkbox::class]);
$groupCode->addControl('css', [Form\Control\Checkbox::class]);

$groupOther = $formGroup->addGroup(['Others']);
$groupOther->addControl('language', ['width' => 'eight']);
$groupOther->addControl('favorite_pet', ['width' => 'four']);

// To hide-show group simply select a field in that group.
// Show group where 'php' belong when dev is checked.
// Show group where 'language' belong when dev is checked.
$formGroup->setGroupDisplayRules(['php' => ['dev' => 'checked'], 'language' => ['dev' => 'checked']]);

//////////////////////////////////////////////////////////
/*
\Atk4\Ui\Header::addTo($app, ['Hide or show accordion section', 'size'=>2]);

$f_acc = Form::addTo($app, ['segment']);
\Atk4\Ui\Label::addTo($f_acc, ['Work on section layouts too.', 'top attached'], ['AboveControls']);

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

// To hide-show group or section simply select a field in that group.
// Show group where 'php' belong when dev is checked.
// Show group where 'language' belong when dev is checked.
$f_acc->setGroupDisplayRules(
    // rules
    ['addr2' => ['custom_shipping' => 'checked']]

    // JS selector of container
    //,'.atk-form-group'        // this will hide group
    //,'.content'               // this will hide content of 2nd accordion section
    , $ship_section->getOwner() // this way we set selector to accordion section title block - so what? we still can't do anything about it
    //                          // BUT there is no way how to show/hide all accordion section including title and content
);
*/
