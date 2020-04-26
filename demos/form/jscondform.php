<?php

require_once __DIR__ . '/../atk-init.php';

//////////////////////////////////////////////////////////
\atk4\ui\Header::addTo($app, ['Phone', 'size'=>2]);

$f_phone = \atk4\ui\Form::addTo($app, ['segment']);
\atk4\ui\Label::addTo($f_phone, ['Add other phone field input. Note: phone1 required a number of at least 5 char.', 'top attached'], ['AboveFields']);

$f_phone->addField('phone1');
$f_phone->addField('phone2');
$f_phone->addField('phone3');
$f_phone->addField('phone4');

// Show phoneX when previous phone is visible and has a number with at least 5 char.
$f_phone->setFieldsDisplayRules([
    'phone2' => ['phone1' => ['number', 'minLength[5]']],
    'phone3' => ['phone2' => ['number', 'minLength[5]'], 'phone1' => ['number', 'minLength[5]']],
    'phone4' => ['phone3' => ['number', 'minLength[5]'], 'phone2' => ['number', 'minLength[5]'], 'phone1' => ['number', 'minLength[5]']],
]);

//////////////////////////////////////////////////////////
\atk4\ui\Header::addTo($app, ['Optional subscription', 'size'=>2]);

$f_sub = \atk4\ui\Form::addTo($app, ['segment']);
\atk4\ui\Label::addTo($f_sub, ['Click on subscribe and add email to receive your gift.', 'top attached'], ['AboveFields']);

$f_sub->addField('name');
$f_sub->addField('subscribe', ['CheckBox', 'Subscribe to weekly newsletter', 'toggle']);
$f_sub->addField('email');
$f_sub->addField('gender', ['Radio'], ['enum'=>['Female', 'Male']])->set('Female');
$f_sub->addField('m_gift', ['DropDown', 'caption'=>'Gift for Men', 'values' => ['Beer Glass', 'Swiss Knife']]);
$f_sub->addField('f_gift', ['DropDown', 'caption'=>'Gift for Women', 'values' => ['Wine Glass', 'Lipstick']]);

// Show email and gender when subscribe is checked.
// Show m_gift when gender is exactly equal to 'male' and subscribe is checked.
// Show f_gift when gender is exactly equal to 'female' and subscribe is checked.
$f_sub->setFieldsDisplayRules([
    'email' => ['subscribe' => 'checked'],
    'gender'=> ['subscribe' => 'checked'],
    'm_gift'=> ['gender' => 'isExactly[Male]', 'subscribe' => 'checked'],
    'f_gift'=> ['gender' => 'isExactly[Female]', 'subscribe' => 'checked'],
]);

//////////////////////////////////////////////////////////
\atk4\ui\Header::addTo($app, ['Dog registration', 'size'=>2]);

$f_dog = \atk4\ui\Form::addTo($app, ['segment']);
\atk4\ui\Label::addTo($f_dog, ['You can select type of hair cut only with race that contains "poodle" AND age no more than 5 year OR your dog race is exactly "bichon".', 'top attached'], ['AboveFields']);
$f_dog->addField('race', ['Line']);
$f_dog->addField('age');
$f_dog->addField('hair_cut', ['DropDown', 'values' => ['Short', 'Long']]);

// Show 'hair_cut' when race contains the word 'poodle' AND age is between 1 and 5
// OR
// Show 'hair_cut' when race contains exactly the word 'bichon'
$f_dog->setFieldsDisplayRules([
    'hair_cut' => [['race' => 'contains[poodle]', 'age'=>'integer[1..5]'], ['race' => 'isExactly[bichon]']],
]);

//////////////////////////////////////////////////////////
\atk4\ui\Header::addTo($app, ['Hide or show group', 'size'=>2]);

$f_group = \atk4\ui\Form::addTo($app, ['segment']);
\atk4\ui\Label::addTo($f_group, ['Work on form group too.', 'top attached'], ['AboveFields']);

$g_basic = $f_group->addGroup(['Basic Information']);
$g_basic->addField('first_name', ['width' => 'eight']);
$g_basic->addField('middle_name', ['width' => 'three']);
$g_basic->addField('last_name', ['width' => 'five']);

$f_group->addField('dev', ['CheckBox', 'caption' => 'I am a developper']);

$g_code = $f_group->addGroup(['Check all language that apply']);
$g_code->addField('php', ['CheckBox']);
$g_code->addField('js', ['CheckBox']);
$g_code->addField('html', ['CheckBox']);
$g_code->addField('css', ['CheckBox']);

$g_other = $f_group->addGroup(['Others']);
$g_other->addField('language', ['width' => 'eight']);
$g_other->addField('favorite_pet', ['width' => 'four']);

//To hide-show group simply select a field in that group.
// Show group where 'php' belong when dev is checked.
// Show group where 'language' belong when dev is checked.
$f_group->setGroupDisplayRules(['php' => ['dev' => 'checked'], 'language'=>['dev'=>'checked']]);

//////////////////////////////////////////////////////////
/*
\atk4\ui\Header::addTo($app, ['Hide or show accordion section', 'size'=>2]);

$f_acc = \atk4\ui\Form::addTo($app, ['segment']);
\atk4\ui\Label::addTo($f_acc, ['Work on section layouts too.', 'top attached'], ['AboveFields']);

// Accordion
$accordion_layout = $f_acc->layout->addSubLayout(['Accordion', 'type' => ['styled', 'fluid'], 'settings' => ['exclusive' => false]]);

// Section - business address
$adr_section = $accordion_layout->addSection('Business Address');

$gr = $adr_section->addGroup('Street and City');
$gr->addField('addr1', ['width' => 'eight'], ['required'=>true]);
$gr->addField('city1', ['width' => 'eight']);

$gr = $adr_section->addGroup('State, Country and Postal Code');
$gr->addField('state1', ['width' => 'six']);
$gr->addField('country1', ['width' => 'six']);
$gr->addField('postal1', ['width' => 'four']);

$adr_section->addField('custom_shipping', ['CheckBox', 'caption'=>'Different Shipping Address']);

// Section - shipping address
$ship_section = $accordion_layout->addSection('Shipping address');

$gr = $ship_section->addGroup('Street and City');
$gr->addField('addr2', ['width' => 'eight'], ['required'=>true]);
$gr->addField('city2', ['width' => 'eight']);

$gr = $ship_section->addGroup('State, Country and Postal Code');
$gr->addField('state2', ['width' => 'six']);
$gr->addField('country2', ['width' => 'six']);
$gr->addField('postal2', ['width' => 'four']);

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
