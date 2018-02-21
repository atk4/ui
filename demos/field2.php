<?php
/**
 * Demonstrates how to use fields with form.
 */
require 'init.php';

$app->add(['Header', 'Stand Alone Line']);
// you can pass values to button
$field = $app->add(new \atk4\ui\FormField\Line());

$field->set('hello world');

$button = $field->addAction('check value');
$button->on('click', new \atk4\ui\jsExpression('alert("field value is: "+[])', [$field->jsInput()->val()]));

$app->add(['Header', 'Line in a Form']);
$form = $app->add('Form');

$field = $form->addField('Title', null, ['values'=>['Mr','Mrs','Miss'], 'ui'=>['hint'=>'select one']]);

$field = $form->addField('name', ['Line', 'hint'=>'this is sample hint that escapes <html> characters']);
$field->set('value in a form');


$field = $form->addField('surname', new \atk4\ui\FormField\Line([
    'hint'=>['template'=>new \atk4\ui\Template(
        'Click <a href="http://example.com/" target="_blank">here</a>'
    )]
]));

$form->onSubmit(function ($f) {
    return $f->model['name'];
});

$app->add(['Header', 'Multiple Form Layouts']);

$form = $app->add('Form');
$tabs = $form->add('Tabs', 'AboveFields');
$form->add(['ui' => 'divider'], 'AboveFields');

$form_page = $tabs->addTab('Basic Info')->add(['FormLayout\Generic', 'form' => $form]);
$form_page->addField('name', new \atk4\ui\FormField\Line());

$form_page = $tabs->addTab('Other Info')->add(['FormLayout\Generic', 'form' => $form]);
$form_page->addField('age', new \atk4\ui\FormField\Line());

$form->onSubmit(function ($f) {
    return $f->model['name'].' has age '.$f->model['age'];
});
