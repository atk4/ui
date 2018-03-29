<?php

require 'init.php';

$app->add(['Header', 'Phone', 'size'=>2]);

$f_phone = $app->add(new \atk4\ui\Form(['segment']));
$f_phone->add(['Label', 'Add other phone field input. Note: phone1 required a number of at least 5 char.', 'top attached'], 'AboveFields');

$f_phone->addField('phone1');
$f_phone->addField('phone2');
$f_phone->addField('phone3');
$f_phone->addField('phone4');

$f_phone->js(true)->atkConditionalForm(['fieldRules' => [
                                                            'phone2' => ['phone1' => ['number', 'minLength[5]']],
                                                            'phone3' => ['phone2' => 'notEmpty', 'phone1' => 'notEmpty'],
                                                            'phone4' => ['phone2' => 'notEmpty', 'phone1' => 'notEmpty', 'phone3' => 'notEmpty'],
                                                        ],
                                       ]);

$app->add(['Header', 'Optional subscription', 'size'=>2]);


$f_sub = $app->add( new \atk4\ui\Form( ['segment']));
$f_sub->add(['Label', 'Click on subscribe and add email to receive your gift.', 'top attached'], 'AboveFields');

$f_sub->addField('name');
$f_sub->addField('subscribe', ['CheckBox', 'Subscribe to weekly newsletter', 'toggle']);
$f_sub->addField('email');
$f_sub->addField('gender',  ['Radio'], ['enum'=>['Female', 'Male']])->set('Female');
$f_sub->addField('m_gift', ['DropDown', 'caption'=>'Gift for Men', 'values' => ['Beer Glass', 'Swiss Knife']]);
$f_sub->addField('f_gift', ['DropDown', 'caption'=>'Gift for Women', 'values' => ['Wine Glass', 'Lipstick']]);


$f_sub->js(true)->atkConditionalForm(['fieldRules' => [
                                                        'email' => ['subscribe' => 'checked'],
                                                        'gender'=> ['subscribe' => 'checked'],
                                                        'm_gift'=> ['gender' => 'isExactly[Male]', 'subscribe' => 'checked'],
                                                        'f_gift'=> ['gender' => 'isExactly[Female]', 'subscribe' => 'checked'],
                                                     ]
                                    ]);

$app->add(['Header', 'Dog registration', 'size'=>2]);


$f_dog = $app->add( new \atk4\ui\Form( ['segment']));
$f_dog->add(['Label', 'You can select type of hair cut only with race that contains "poodle" AND age no more than 5 year OR your dog race is exactly "bichon".', 'top attached'], 'AboveFields');
$f_dog->addField('race', ['Line']);
$f_dog->addField('age');
$f_dog->addField('hair_cut', ['DropDown', 'values' => ['Short', 'Long']]);

$f_dog->js( true)->atkConditionalForm( [ 'fieldRules' => [
                                        'hair_cut' => [['race' => 'contains[poodle]', 'age'=>'integer[0..5]'],['race' => 'isExactly[bichon]']],
                                    ]
                                   ]);
