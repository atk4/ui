<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Label;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// -----------------------------------------------------------------------------

Header::addTo($app, ['Phone', 'size' => 2]);

$formPhone = Form::addTo($app, ['class.segment' => true]);
Label::addTo($formPhone, ['Add other phone field input. Note: phone1 required a number of at least 5 char.', 'class.top attached' => true], ['AboveControls']);

$formPhone->addControl('phone1');
$formPhone->addControl('phone2');
$formPhone->addControl('phone3');
$formPhone->addControl('phone4');

// show phoneX when previous phone is visible and has a number with at least 5 char
$formPhone->setControlsDisplayRules([
    'phone2' => ['phone1' => ['number', 'minLength[5]']],
    'phone3' => ['phone2' => ['number', 'minLength[5]'], 'phone1' => ['number', 'minLength[5]']],
    'phone4' => ['phone3' => ['number', 'minLength[5]'], 'phone2' => ['number', 'minLength[5]'], 'phone1' => ['number', 'minLength[5]']],
]);

// -----------------------------------------------------------------------------

Header::addTo($app, ['Optional subscription', 'size' => 2]);

$formSubscribe = Form::addTo($app, ['class.segment' => true]);
Label::addTo($formSubscribe, ['Click on subscribe and add email to receive your gift.', 'class.top attached' => true], ['AboveControls']);

$formSubscribe->addControl('name');
$formSubscribe->addControl('subscribe', [Form\Control\Checkbox::class, 'Subscribe to weekly newsletter', 'class.toggle' => true]);
$formSubscribe->addControl('email');
$formSubscribe->addControl('gender', [Form\Control\Radio::class], ['enum' => ['Female', 'Male']])->set('Female');
$formSubscribe->addControl('m_gift', [Form\Control\Dropdown::class, 'caption' => 'Gift for Men', 'values' => ['Beer Glass', 'Swiss Knife']]);
$formSubscribe->addControl('f_gift', [Form\Control\Dropdown::class, 'caption' => 'Gift for Women', 'values' => ['Wine Glass', 'Lipstick']]);

// show email and gender when subscribe is checked
// show m_gift when gender = 'male' and subscribe is checked
// show f_gift when gender = 'female' and subscribe is checked
$formSubscribe->setControlsDisplayRules([
    'email' => ['subscribe' => 'checked'],
    'gender' => ['subscribe' => 'checked'],
    'm_gift' => ['gender' => 'isExactly[Male]', 'subscribe' => 'checked'],
    'f_gift' => ['gender' => 'isExactly[Female]', 'subscribe' => 'checked'],
]);

// -----------------------------------------------------------------------------

Header::addTo($app, ['Dog registration', 'size' => 2]);

$formDog = Form::addTo($app, ['class.segment' => true]);
Label::addTo($formDog, ['You can select type of hair cut only with race that contains "poodle" AND age no more than 5 year OR your dog race equals "bichon".', 'class.top attached' => true], ['AboveControls']);
$formDog->addControl('race', [Form\Control\Line::class]);
$formDog->addControl('age');
$formDog->addControl('hair_cut', [Form\Control\Dropdown::class, 'values' => ['Short', 'Long']]);

// show 'hair_cut' when race contains the word 'poodle' AND age is between 1 and 5
// OR
// show 'hair_cut' when race contains exactly the word 'bichon'
$formDog->setControlsDisplayRules([
    'hair_cut' => [['race' => 'contains[poodle]', 'age' => 'integer[1..5]'], ['race' => 'isExactly[bichon]']],
]);

// -----------------------------------------------------------------------------

Header::addTo($app, ['Hide or show group', 'size' => 2]);

$formGroup = Form::addTo($app, ['class.segment' => true]);
Label::addTo($formGroup, ['Work on form group too.', 'class.top attached' => true], ['AboveControls']);

$groupBasic = $formGroup->addGroup(['Basic Information']);
$groupBasic->addControl('first_name', ['width' => 'eight']);
$groupBasic->addControl('middle_name', ['width' => 'three']);
$groupBasic->addControl('last_name', ['width' => 'five']);

$formGroup->addControl('dev', [Form\Control\Checkbox::class, 'caption' => 'I am a developer']);

$groupCode = $formGroup->addGroup(['Check all language that apply']);
$groupCode->addControl('php', [Form\Control\Checkbox::class]);
$groupCode->addControl('js', [Form\Control\Checkbox::class]);
$groupCode->addControl('html', [Form\Control\Checkbox::class]);
$groupCode->addControl('css', [Form\Control\Checkbox::class]);

$groupOther = $formGroup->addGroup(['Others']);
$groupOther->addControl('language', ['width' => 'eight']);
$groupOther->addControl('favorite_pet', ['width' => 'four']);

// to hide-show group simply select a field in that group
// show group where 'php' belong when dev is checked
// show group where 'language' belong when dev is checked
$formGroup->setGroupDisplayRules(['php' => ['dev' => 'checked'], 'language' => ['dev' => 'checked']]);

// -----------------------------------------------------------------------------

Header::addTo($app, ['Hide or show accordion section', 'size' => 2]);

$formAccordion = Form::addTo($app, ['class.segment' => true]);
Label::addTo($formAccordion, ['Work on section layouts too.', 'class.top attached' => true], ['AboveControls']);

$accordionLayout = $formAccordion->layout->addSubLayout([Form\Layout\Section\Accordion::class, 'type' => ['styled', 'fluid'], 'settings' => ['exclusive' => false]]);

$invoiceAddressSection = $accordionLayout->addSection('Invoice Address');
$group = $invoiceAddressSection->addGroup('Street and City');
$group->addControl('invoice_addr', ['width' => 'eight'], ['required' => true]);
$group->addControl('invoice_city', ['width' => 'eight']);
$group = $invoiceAddressSection->addGroup('State, Country and Postal Code');
$group->addControl('invoice_state', ['width' => 'six']);
$group->addControl('country', ['width' => 'six']);
$group->addControl('invoice_postal', ['width' => 'four']);

$invoiceAddressSection->addControl('has_custom_delivery_address', [Form\Control\Checkbox::class, 'caption' => 'Different Delivery Address']);

$deliveryAddressSection = $accordionLayout->addSection('Delivery Address');
$group = $deliveryAddressSection->addGroup('Street and City');
$group->addControl('delivery_addr', ['width' => 'eight'], ['required' => true]);
$group->addControl('delivery_city', ['width' => 'eight']);
$group = $deliveryAddressSection->addGroup('State, Country and Postal Code');
$group->addControl('delivery_state', ['width' => 'six']);
$group->addControl('delivery_country', ['width' => 'six']);
$group->addControl('delivery_postal', ['width' => 'four']);

$accordionLayout->activate($invoiceAddressSection);

// to hide-show group or section simply select a field in that group
// show group where 'php' belong when dev is checked
// show group where 'language' belong when dev is checked
$formAccordion->setGroupDisplayRules(
    ['delivery_addr' => ['has_custom_delivery_address' => 'checked']],

    // TODO not implemented
    // JS selector of container
    // '.atk-form-group' // this will hide group
    // '.content' // this will hide content of 2nd accordion section
    $deliveryAddressSection->getOwner() // this way we set selector to accordion section title block - so what? we still can't do anything about it
    // BUT there is no way how to show/hide all accordion section including title and content
);
