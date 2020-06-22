<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/**
 * Demonstrates how to use fields with form.
 */
/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

use atk4\ui\Form;

\atk4\ui\Header::addTo($app, ['Disabled and read only form fields (normal / readonly / disabled)']);

$form = Form::addTo($app);

// Test all kinds of input fields
$group = $form->addGroup('Line');
$group->addField('line_norm')->set('editable');
$group->addField('line_read', ['readonly' => true])->set('read only');
$group->addField('line_disb', ['disabled' => true])->set('disabled');

$group = $form->addGroup('Text Area');
$group->addField('text_norm', [new Form\Field\TextArea()])->set('editable');
$group->addField('text_read', [new Form\Field\TextArea(), 'readonly' => true])->set('read only');
$group->addField('text_disb', [new Form\Field\TextArea(), 'disabled' => true])->set('disabled');

$group = $form->addGroup('Checkbox');
$group->addField('c_norm', [new Form\Field\Checkbox()])->set(true);
$group->addField('c_read', [new Form\Field\Checkbox(), 'readonly' => true])->set(true); // allows to change value
$group->addField('c_disb', [new Form\Field\Checkbox(), 'disabled' => true])->set(true); // input is not disabled

$group = $form->addGroup('Dropdown');
$values = [
    'tag' => ['Tag', 'icon' => 'tag icon'],
    'globe' => ['Globe', 'icon' => 'globe icon'],
    'registered' => ['Registered', 'icon' => 'registered icon'],
    'file' => ['File', 'icon' => 'file icon'],
];
$group->addField('d_norm', [new Form\Field\Dropdown(['values' => $values]), 'width' => 'three'])->set('globe');
$group->addField('d_read', [new Form\Field\Dropdown(['values' => $values]), 'readonly' => true, 'width' => 'three'])->set('globe'); // allows to change value
$group->addField('d_disb', [new Form\Field\Dropdown(['values' => $values]), 'disabled' => true, 'width' => 'three'])->set('globe'); // css disabled, but can focus with Tab and change value

$group = $form->addGroup('Radio');

$group->addField('radio_norm', [Form\Field\Radio::class], ['enum' => ['one', 'two', 'three']])->set('two');
$group->addField('radio_read', [Form\Field\Radio::class, 'readonly' => true], ['enum' => ['one', 'two', 'three']])->set('two');
$group->addField('radio_disb', [Form\Field\Radio::class, 'disabled' => true], ['enum' => ['one', 'two', 'three']])->set('two');

$group = $form->addGroup('File upload');

$onDelete = function () {
};
$onUpload = function () {
};

$field = $group->addField('file_norm', [Form\Field\Upload::class, ['accept' => ['.png', '.jpg']]])->set('normal', 'normal.jpg');
$field->onDelete($onDelete);
$field->onUpload($onUpload);

$field = $group->addField('file_read', [Form\Field\Upload::class, ['accept' => ['.png', '.jpg'], 'readonly' => true]])->set('readonly', 'readonly.jpg');
$field->onDelete($onDelete);
$field->onUpload($onUpload);

$field = $group->addField('file_disb', [Form\Field\Upload::class, ['accept' => ['.png', '.jpg'], 'disabled' => true]])->set('disabled', 'disabled.jpg');
$field->onDelete($onDelete);
$field->onUpload($onUpload);

$group = $form->addGroup('Lookup');

$m = new Country($app->db);

$group->addField('Lookup_norm', [
    new DemoLookup(),
    'model' => new CountryLock($app->db),
    'plus' => true,
])->set($m->loadAny()->id);

$group->addField('Lookup_read', [
    Form\Field\Lookup::class,
    'model' => new CountryLock($app->db),
    'plus' => true,
    'readonly' => true,
])->set($m->loadAny()->id);

$group->addField('Lookup_disb', [
    Form\Field\Lookup::class,
    'model' => new CountryLock($app->db),
    'plus' => true,
    'disabled' => true,
])->set($m->loadAny()->id);

$group = $form->addGroup('Calendar');

$group->addField('calendar_norm', [Form\Field\Calendar::class, 'type' => 'date'])->set(date($app->ui_persistence->date_format));
$group->addField('calendar_read', [Form\Field\Calendar::class, 'type' => 'date', 'readonly' => true])->set(date($app->ui_persistence->date_format));
$group->addField('calendar_disb', [Form\Field\Calendar::class, 'type' => 'date', 'disabled' => true])->set(date($app->ui_persistence->date_format));

\atk4\ui\Header::addTo($app, ['Stand Alone Line']);
// you can pass values to button
$field = Form\Field\Line::addTo($app);

$field->set('hello world');

$button = $field->addAction('check value');
$button->on('click', new \atk4\ui\jsExpression('alert("field value is: "+[])', [$field->jsInput()->val()]));

\atk4\ui\Header::addTo($app, ['Line in a Form']);
$form = Form::addTo($app);

$field = $form->addField('Title', null, ['values' => ['Mr', 'Mrs', 'Miss'], 'ui' => ['hint' => 'select one']]);

$field = $form->addField('name', [Form\Field\Line::class, 'hint' => 'this is sample hint that escapes <html> characters']);
$field->set('value in a form');

$field = $form->addField('surname', new Form\Field\Line([
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
$form_page->addField('name', new Form\Field\Line());

$form_page = Form\Layout::addTo($tabs->addTab('Other Info'), ['form' => $form]);
$form_page->addField('age', new Form\Field\Line());

$form->onSubmit(function (Form $form) {
    return $form->model->get('name') . ' has age ' . $form->model->get('age');
});

\atk4\ui\Header::addTo($app, ['onChange event', 'subHeader' => 'see in browser console']);

$form = Form::addTo($app);

$group = $form->addGroup('Calendar');
$c1 = $group->addField('c1', new Form\Field\Calendar(['type' => 'date']));
$c2 = $group->addField('c2', new Form\Field\Calendar(['type' => 'date']));
$c3 = $group->addField('c3', new Form\Field\Calendar(['type' => 'date']));

$c1->onChange('console.log("c1 changed: "+date+","+text+","+mode)');
$c2->onChange(new \atk4\ui\jsExpression('console.log("c2 changed: "+date+","+text+","+mode)'));
$c3->onChange([
    new \atk4\ui\jsExpression('console.log("c3 changed: "+date+","+text+","+mode)'),
    new \atk4\ui\jsExpression('console.log("c3 really changed: "+date+","+text+","+mode)'),
]);

$group = $form->addGroup('Line');
$f1 = $group->addField('f1');
$f2 = $group->addField('f2');
$f3 = $group->addField('f3');
$f4 = $group->addField('f4');

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
$b1 = $group->addField('b1', new Form\Field\Checkbox());
$b1->onChange('console.log("b1 changed")');

$group = $form->addGroup(['Dropdown', 'width' => 'three']);
$d1 = $group->addField('d1', new Form\Field\Dropdown(['values' => [
    'tag' => ['Tag', 'icon' => 'tag icon'],
    'globe' => ['Globe', 'icon' => 'globe icon'],
    'registered' => ['Registered', 'icon' => 'registered icon'],
    'file' => ['File', 'icon' => 'file icon'],
],
]));
$d1->onChange('console.log("Dropdown changed")');

$group = $form->addGroup('Radio');
$r1 = $group->addField('r1', new Form\Field\Radio(['values' => [
    'Tag',
    'Globe',
    'Registered',
    'File',
],
]));
$r1->onChange('console.log("radio changed")');

\atk4\ui\Header::addTo($app, ['Line ends of TextArea']);

$form = Form::addTo($app);
$group = $form->addGroup('Without model');
$group->addField('text_crlf', [new Form\Field\TextArea()])->set("First line\r\nSecond line");
$group->addField('text_cr', [new Form\Field\TextArea()])->set("First line\rSecond line");
$group->addField('text_lf', [new Form\Field\TextArea()])->set("First line\nSecond line");

$group = $form->addGroup('With model');
$group->addField('m_text_crlf', [new Form\Field\TextArea()], ['type' => 'text'])->set("First line\r\nSecond line");
$group->addField('m_text_cr', [new Form\Field\TextArea()], ['type' => 'text'])->set("First line\rSecond line");
$group->addField('m_text_lf', [new Form\Field\TextArea()], ['type' => 'text'])->set("First line\nSecond line");

$form->onSubmit(function (Form $form) {
    // check what values are submitted
    echo "We're URL encoding submitted values to be able to see what line end is actually submitted.";
    foreach ($form->model->get() as $k => $v) {
        var_dump([$k => urlencode($v)]);
    }
    echo 'As you can see - without model it submits CRLF, but with model it will normalize all to LF';
});
