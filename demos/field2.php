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

$field = $form->addField('Title', null, ['values'=>['Mr', 'Mrs', 'Miss'], 'ui'=>['hint'=>'select one']]);

$field = $form->addField('name', ['Line', 'hint'=>'this is sample hint that escapes <html> characters']);
$field->set('value in a form');

$field = $form->addField('surname', new \atk4\ui\FormField\Line([
    'hint'=> ['template'=> new \atk4\ui\Template(
        'Click <a href="http://example.com/" target="_blank">here</a>'
    )],
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



$app->add(['Header', 'onChange event', 'subHeader'=>'see in browser console']);

$form = $app->add('Form');

$g = $form->addGroup('Calendar');
$c1 = $g->addField('c1', new \atk4\ui\FormField\Calendar(['type'=>'date']));
$c2 = $g->addField('c2', new \atk4\ui\FormField\Calendar(['type'=>'date']));
$c3 = $g->addField('c3', new \atk4\ui\FormField\Calendar(['type'=>'date']));

$c1->onChange('console.log("c1 changed: "+date+","+text+","+mode)');
$c2->onChange(new \atk4\ui\jsExpression('console.log("c2 changed: "+date+","+text+","+mode)'));
$c3->onChange([
    new \atk4\ui\jsExpression('console.log("c3 changed: "+date+","+text+","+mode)'),
    new \atk4\ui\jsExpression('console.log("c3 really changed: "+date+","+text+","+mode)'),
]);

$g = $form->addGroup('Line');
$f1 = $g->addField('f1');
$f2 = $g->addField('f2');
$f3 = $g->addField('f3');
$f4 = $g->addField('f4');

$f1->onChange('console.log("f1 changed")');
$f2->onChange(new \atk4\ui\jsExpression('console.log("f2 changed")'));
$f3->onChange([
    new \atk4\ui\jsExpression('console.log("f3 changed")'),
    new \atk4\ui\jsExpression('console.log("f3 really changed")'),
]);
$f4->onChange(function () {
    return new \atk4\ui\jsExpression('console.log("f4 changed")');
});

$g = $form->addGroup('CheckBox');
$b1 = $g->addField('b1', new \atk4\ui\FormField\CheckBox());
$b1->onChange('console.log("b1 changed")');

$g = $form->addGroup('DropDown');
$d1 = $g->addField('d1', new \atk4\ui\FormField\DropDown(['values' => [
            'tag'        => ['Tag', 'icon' => 'tag icon'],
            'globe'      => ['Globe', 'icon' => 'globe icon'],
            'registered' => ['Registered', 'icon' => 'registered icon'],
            'file'       => ['File', 'icon' => 'file icon'],
        ]
]));
$d1->onChange('console.log("d1 changed")');

$g = $form->addGroup('Radio');
$r1 = $g->addField('r1', new \atk4\ui\FormField\Radio(['values' => [
            'Tag',
            'Globe',
            'Registered',
            'File',
        ]
]));
$r1->onChange('console.log("r1 changed")');

