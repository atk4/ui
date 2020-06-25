<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Form;

/**
 * Demonstrates how to use fields with form.
 */
/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

\atk4\ui\Header::addTo($app, ['Disabled and read only form fields (normal / readonly / disabled)']);

$form = Form::addTo($app);

// Test all kinds of input fields
$group = $form->addGroup('Line');
$group->addControl('line_norm')->set('editable');
$group->addControl('line_read', ['readonly' => true])->set('read only');
$group->addControl('line_disb', ['disabled' => true])->set('disabled');

$group = $form->addGroup('Text Area');
$group->addControl('text_norm', [new Form\Control\Textarea()])->set('editable');
$group->addControl('text_read', [new Form\Control\Textarea(), 'readonly' => true])->set('read only');
$group->addControl('text_disb', [new Form\Control\Textarea(), 'disabled' => true])->set('disabled');

$group = $form->addGroup('Checkbox');
$group->addControl('c_norm', [new Form\Control\Checkbox()])->set(true);
$group->addControl('c_read', [new Form\Control\Checkbox(), 'readonly' => true])->set(true); // allows to change value
$group->addControl('c_disb', [new Form\Control\Checkbox(), 'disabled' => true])->set(true); // input is not disabled

$group = $form->addGroup('Dropdown');
$values = [
    'tag' => ['Tag', 'icon' => 'tag icon'],
    'globe' => ['Globe', 'icon' => 'globe icon'],
    'registered' => ['Registered', 'icon' => 'registered icon'],
    'file' => ['File', 'icon' => 'file icon'],
];
$group->addControl('d_norm', [new Form\Control\Dropdown(['values' => $values]), 'width' => 'three'])->set('globe');
$group->addControl('d_read', [new Form\Control\Dropdown(['values' => $values]), 'readonly' => true, 'width' => 'three'])->set('globe'); // allows to change value
$group->addControl('d_disb', [new Form\Control\Dropdown(['values' => $values]), 'disabled' => true, 'width' => 'three'])->set('globe'); // css disabled, but can focus with Tab and change value

$group = $form->addGroup('Radio');

$group->addControl('radio_norm', [Form\Control\Radio::class], ['enum' => ['one', 'two', 'three']])->set('two');
$group->addControl('radio_read', [Form\Control\Radio::class, 'readonly' => true], ['enum' => ['one', 'two', 'three']])->set('two');
$group->addControl('radio_disb', [Form\Control\Radio::class, 'disabled' => true], ['enum' => ['one', 'two', 'three']])->set('two');

$group = $form->addGroup('File upload');

$onDelete = function () {
};
$onUpload = function () {
};

$field = $group->addControl('file_norm', [Form\Control\Upload::class, ['accept' => ['.png', '.jpg']]])->set('normal', 'normal.jpg');
$field->onDelete($onDelete);
$field->onUpload($onUpload);

$field = $group->addControl('file_read', [Form\Control\Upload::class, ['accept' => ['.png', '.jpg'], 'readonly' => true]])->set('readonly', 'readonly.jpg');
$field->onDelete($onDelete);
$field->onUpload($onUpload);

$field = $group->addControl('file_disb', [Form\Control\Upload::class, ['accept' => ['.png', '.jpg'], 'disabled' => true]])->set('disabled', 'disabled.jpg');
$field->onDelete($onDelete);
$field->onUpload($onUpload);

$group = $form->addGroup('Lookup');

$m = new Country($app->db);

$group->addControl('Lookup_norm', [
    new DemoLookup(),
    'model' => new CountryLock($app->db),
    'plus' => true,
])->set($m->loadAny()->id);

$group->addControl('Lookup_read', [
    Form\Control\Lookup::class,
    'model' => new CountryLock($app->db),
    'plus' => true,
    'readonly' => true,
])->set($m->loadAny()->id);

$group->addControl('Lookup_disb', [
    Form\Control\Lookup::class,
    'model' => new CountryLock($app->db),
    'plus' => true,
    'disabled' => true,
])->set($m->loadAny()->id);

$group = $form->addGroup('Calendar');

$group->addControl('calendar_norm', [Form\Control\Calendar::class, 'type' => 'date'])->set(date($app->ui_persistence->date_format));
$group->addControl('calendar_read', [Form\Control\Calendar::class, 'type' => 'date', 'readonly' => true])->set(date($app->ui_persistence->date_format));
$group->addControl('calendar_disb', [Form\Control\Calendar::class, 'type' => 'date', 'disabled' => true])->set(date($app->ui_persistence->date_format));

\atk4\ui\Header::addTo($app, ['Stand Alone Line']);
// you can pass values to button
$field = Form\Control\Line::addTo($app);

$field->set('hello world');

$button = $field->addAction('check value');
$button->on('click', new \atk4\ui\jsExpression('alert("field value is: "+[])', [$field->jsInput()->val()]));

\atk4\ui\Header::addTo($app, ['Line in a Form']);
$form = Form::addTo($app);

$field = $form->addControl('Title', null, ['values' => ['Mr', 'Mrs', 'Miss'], 'ui' => ['hint' => 'select one']]);

$field = $form->addControl('name', [Form\Control\Line::class, 'hint' => 'this is sample hint that escapes <html> characters']);
$field->set('value in a form');

$field = $form->addControl('surname', new Form\Control\Line([
    'hint' => [\atk4\ui\View::class, 'template' => new \atk4\ui\Template(
        'Click <a href="http://example.com/" target="_blank">here</a>'
    )],
]));

$form->onSubmit(function (Form $form) {
    return $form->model->get('name');
});

\atk4\ui\Header::addTo($app, ['Multiple Form Layouts']);

$form = Form::addTo($app);
$tabs = \atk4\ui\Tabs::addTo($form, [], ['AboveFields']);
\atk4\ui\View::addTo($form, ['ui' => 'divider'], ['AboveFields']);

$form_page = Form\Layout::addTo($tabs->addTab('Basic Info'), ['form' => $form]);
$form_page->addControl('name', new Form\Control\Line());

$form_page = Form\Layout::addTo($tabs->addTab('Other Info'), ['form' => $form]);
$form_page->addControl('age', new Form\Control\Line());

$form->onSubmit(function (Form $form) {
    return $form->model->get('name') . ' has age ' . $form->model->get('age');
});

\atk4\ui\Header::addTo($app, ['onChange event', 'subHeader' => 'see in browser console']);

$form = Form::addTo($app);

$group = $form->addGroup('Calendar');
$c1 = $group->addControl('c1', new Form\Control\Calendar(['type' => 'date']));
$c2 = $group->addControl('c2', new Form\Control\Calendar(['type' => 'date']));
$c3 = $group->addControl('c3', new Form\Control\Calendar(['type' => 'date']));

$c1->onChange('console.log("c1 changed: "+date+","+text+","+mode)');
$c2->onChange(new \atk4\ui\jsExpression('console.log("c2 changed: "+date+","+text+","+mode)'));
$c3->onChange([
    new \atk4\ui\jsExpression('console.log("c3 changed: "+date+","+text+","+mode)'),
    new \atk4\ui\jsExpression('console.log("c3 really changed: "+date+","+text+","+mode)'),
]);

$group = $form->addGroup('Line');
$f1 = $group->addControl('f1');
$f2 = $group->addControl('f2');
$f3 = $group->addControl('f3');
$f4 = $group->addControl('f4');

$f1->onChange('console.log("f1 changed")');
$f2->onChange(new \atk4\ui\jsExpression('console.log("f2 changed")'));
$f3->onChange([
    new \atk4\ui\jsExpression('console.log("f3 changed")'),
    new \atk4\ui\jsExpression('console.log("f3 really changed")'),
]);
$f4->onChange(function () {
    return new \atk4\ui\jsExpression('console.log("f4 changed")');
});

$group = $form->addGroup('CheckBox');
$b1 = $group->addControl('b1', new Form\Control\Checkbox());
$b1->onChange('console.log("b1 changed")');

$group = $form->addGroup(['Dropdown', 'width' => 'three']);
$d1 = $group->addControl('d1', new Form\Control\Dropdown(['values' => [
    'tag' => ['Tag', 'icon' => 'tag icon'],
    'globe' => ['Globe', 'icon' => 'globe icon'],
    'registered' => ['Registered', 'icon' => 'registered icon'],
    'file' => ['File', 'icon' => 'file icon'],
],
]));
$d1->onChange('console.log("Dropdown changed")');

$group = $form->addGroup('Radio');
$r1 = $group->addControl('r1', new Form\Control\Radio(['values' => [
    'Tag',
    'Globe',
    'Registered',
    'File',
],
]));
$r1->onChange('console.log("radio changed")');

\atk4\ui\Header::addTo($app, ['Line ends of Textarea']);

$form = Form::addTo($app);
$group = $form->addGroup('Without model');
$group->addControl('text_crlf', [new Form\Control\Textarea()])->set("First line\r\nSecond line");
$group->addControl('text_cr', [new Form\Control\Textarea()])->set("First line\rSecond line");
$group->addControl('text_lf', [new Form\Control\Textarea()])->set("First line\nSecond line");

$group = $form->addGroup('With model');
$group->addControl('m_text_crlf', [new Form\Control\Textarea()], ['type' => 'text'])->set("First line\r\nSecond line");
$group->addControl('m_text_cr', [new Form\Control\Textarea()], ['type' => 'text'])->set("First line\rSecond line");
$group->addControl('m_text_lf', [new Form\Control\Textarea()], ['type' => 'text'])->set("First line\nSecond line");

$form->onSubmit(function (Form $form) {
    // check what values are submitted
    echo "We're URL encoding submitted values to be able to see what line end is actually submitted.";
    foreach ($form->model->get() as $k => $v) {
        var_dump([$k => urlencode($v)]);
    }
    echo 'As you can see - without model it submits CRLF, but with model it will normalize all to LF';
});
