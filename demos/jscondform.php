<?php

require 'init.php';

$app->add(['Header', 'Phone', 'size'=>2]);

$f_phone = $app->add(new \atk4\ui\Form(['segment']));
$f_phone->add(['Label', 'Add other phone field input. Note: phone1 required a number of at least 5 char.', 'top attached'], 'AboveFields');

$f_phone->addField('phone1');
$f_phone->addField('phone2');
$f_phone->addField('phone3');
$f_phone->addField('phone4');

// Show phoneX when previous phone is visible and has a number with at least 5 char.
$f_phone->setFieldsDisplayRules([
                                    'phone2' => ['phone1' => ['number', 'minLength[5]']],
                                    'phone3' => ['phone2' => ['number', 'minLength[5]', 'isVisible']],
                                    'phone4' => ['phone3' => ['number', 'minLength[5]', 'isVisible']],
                                ]);

//////////////////////////////////////////////////////////
$app->add(['Header', 'Optional subscription', 'size'=>2]);

$f_sub = $app->add(new \atk4\ui\Form(['segment']));
$f_sub->add(['Label', 'Click on subscribe and add email to receive your gift.', 'top attached'], 'AboveFields');

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
$app->add(['Header', 'Dog registration', 'size'=>2]);

$f_dog = $app->add(new \atk4\ui\Form(['segment']));
$f_dog->add(['Label', 'You can select type of hair cut only with race that contains "poodle" AND age no more than 5 year OR your dog race is exactly "bichon".', 'top attached'], 'AboveFields');
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
$app->add(['Header', 'Hide or show group', 'size'=>2]);

$f_group = $app->add(new \atk4\ui\Form(['segment']));
$f_group->add(['Label', 'Work on form group too.', 'top attached'], 'AboveFields');

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
