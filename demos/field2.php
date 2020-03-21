<?php
/**
 * Demonstrates how to use fields with form.
 */
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

\atk4\ui\Header::addTo($app, ['Disabled and read only form fields (normal / readonly / disabled)']);

$f = \atk4\ui\Form::addTo($app);

// Test all kinds of input fields
$g = $f->addGroup('Line');
$g->addField('line_norm')->set('editable');
$g->addField('line_read', ['readonly' => true])->set('read only');
$g->addField('line_disb', ['disabled' => true])->set('disabled');

$g = $f->addGroup('Text Area');
$g->addField('text_norm', [new \atk4\ui\FormField\TextArea()])->set('editable');
$g->addField('text_read', [new \atk4\ui\FormField\TextArea(), 'readonly' => true])->set('read only');
$g->addField('text_disb', [new \atk4\ui\FormField\TextArea(), 'disabled' => true])->set('disabled');

$g = $f->addGroup('Checkbox');
$g->addField('c_norm', [new \atk4\ui\FormField\CheckBox()])->set(true);
$g->addField('c_read', [new \atk4\ui\FormField\CheckBox(), 'readonly' => true])->set(true); // allows to change value
$g->addField('c_disb', [new \atk4\ui\FormField\CheckBox(), 'disabled' => true])->set(true); // input is not disabled

$g = $f->addGroup('DropDown');
$values = [
    'tag'        => ['Tag', 'icon' => 'tag icon'],
    'globe'      => ['Globe', 'icon' => 'globe icon'],
    'registered' => ['Registered', 'icon' => 'registered icon'],
    'file'       => ['File', 'icon' => 'file icon'],
];
$g->addField('d_norm', [new \atk4\ui\FormField\DropDown(['values' => $values]), 'width'=>'three'])->set('globe');
$g->addField('d_read', [new \atk4\ui\FormField\DropDown(['values' => $values]), 'readonly' => true, 'width'=>'three'])->set('globe'); // allows to change value
$g->addField('d_disb', [new \atk4\ui\FormField\DropDown(['values' => $values]), 'disabled' => true, 'width'=>'three'])->set('globe'); // css disabled, but can focus with Tab and change value

$g = $f->addGroup('Radio');

$g->addField('radio_norm', ['Radio'], ['enum'=>['one', 'two', 'three']])->set('two');
$g->addField('radio_read', ['Radio', 'readonly' => true], ['enum'=>['one', 'two', 'three']])->set('two');
$g->addField('radio_disb', ['Radio', 'disabled' => true], ['enum'=>['one', 'two', 'three']])->set('two');

$g = $f->addGroup('File upload');

$onDelete = function () {
};
$onUpload = function () {
};

$field = $g->addField('file_norm', ['Upload', ['accept' => ['.png', '.jpg']]])->set('normal', 'normal.jpg');
$field->onDelete($onDelete);
$field->onUpload($onUpload);

$field = $g->addField('file_read', ['Upload', ['accept' => ['.png', '.jpg'], 'readonly'=> true]])->set('readonly', 'readonly.jpg');
$field->onDelete($onDelete);
$field->onUpload($onUpload);

$field = $g->addField('file_disb', ['Upload', ['accept' => ['.png', '.jpg'], 'disabled'=> true]])->set('disabled', 'disabled.jpg');
$field->onDelete($onDelete);
$field->onUpload($onUpload);

$g = $f->addGroup('Lookup');

$m = new Country($db);

$g->addField('Lookup_norm', [
    'Lookup',
    'model'       => $m,
    'plus'        => true,
])->set($m->loadAny()->id);

$g->addField('Lookup_read', [
    'Lookup',
    'model'       => $m,
    'plus'        => true,
    'readonly'    => true,
])->set($m->loadAny()->id);

$g->addField('Lookup_disb', [
    'Lookup',
    'model'       => $m,
    'plus'        => true,
    'disabled'    => true,
])->set($m->loadAny()->id);

$g = $f->addGroup('Calendar');

$g->addField('calendar_norm', ['Calendar', 'type' => 'date'])->set(date($app->ui_persistence->date_format));
$g->addField('calendar_read', ['Calendar', 'type' => 'date', 'readonly' => true])->set(date($app->ui_persistence->date_format));
$g->addField('calendar_disb', ['Calendar', 'type' => 'date', 'disabled' => true])->set(date($app->ui_persistence->date_format));

\atk4\ui\Header::addTo($app, ['Stand Alone Line']);
// you can pass values to button
$field = \atk4\ui\FormField\Line::addTo($app);

$field->set('hello world');

$button = $field->addAction('check value');
$button->on('click', new \atk4\ui\jsExpression('alert("field value is: "+[])', [$field->jsInput()->val()]));

\atk4\ui\Header::addTo($app, ['Line in a Form']);
$form = \atk4\ui\Form::addTo($app);

$field = $form->addField('Title', null, ['values'=>['Mr', 'Mrs', 'Miss'], 'ui'=>['hint'=>'select one']]);

$field = $form->addField('name', ['Line', 'hint'=>'this is sample hint that escapes <html> characters']);
$field->set('value in a form');

$field = $form->addField('surname', new \atk4\ui\FormField\Line([
    'hint'=> ['View', 'template'=> new \atk4\ui\Template(
        'Click <a href="http://example.com/" target="_blank">here</a>'
    )],
]));

$form->onSubmit(function ($f) {
    return $f->model['name'];
});

\atk4\ui\Header::addTo($app, ['Multiple Form Layouts']);

$form = \atk4\ui\Form::addTo($app);
$tabs = \atk4\ui\Tabs::addTo($form, [], ['AboveFields']);
\atk4\ui\View::addTo($form, ['ui' => 'divider'], ['AboveFields']);

$form_page = \atk4\ui\FormLayout\Generic::addTo($tabs->addTab('Basic Info'), ['form' => $form]);
$form_page->addField('name', new \atk4\ui\FormField\Line());

$form_page = \atk4\ui\FormLayout\Generic::addTo($tabs->addTab('Other Info'), ['form' => $form]);
$form_page->addField('age', new \atk4\ui\FormField\Line());

$form->onSubmit(function ($f) {
    return $f->model['name'].' has age '.$f->model['age'];
});

\atk4\ui\Header::addTo($app, ['onChange event', 'subHeader'=>'see in browser console']);

$form = \atk4\ui\Form::addTo($app);

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
],
]));
$d1->onChange('console.log("d1 changed")');

$g = $form->addGroup('Radio');
$r1 = $g->addField('r1', new \atk4\ui\FormField\Radio(['values' => [
    'Tag',
    'Globe',
    'Registered',
    'File',
],
]));
$r1->onChange('console.log("r1 changed")');

\atk4\ui\Header::addTo($app, ['Line ends of TextArea']);

$f = \atk4\ui\Form::addTo($app);
$g = $f->addGroup('Without model');
$g->addField('text_crlf', [new \atk4\ui\FormField\TextArea()])->set("First line\r\nSecond line");
$g->addField('text_cr', [new \atk4\ui\FormField\TextArea()])->set("First line\rSecond line");
$g->addField('text_lf', [new \atk4\ui\FormField\TextArea()])->set("First line\nSecond line");

$g = $f->addGroup('With model');
$g->addField('m_text_crlf', [new \atk4\ui\FormField\TextArea()], ['type'=>'text'])->set("First line\r\nSecond line");
$g->addField('m_text_cr', [new \atk4\ui\FormField\TextArea()], ['type'=>'text'])->set("First line\rSecond line");
$g->addField('m_text_lf', [new \atk4\ui\FormField\TextArea()], ['type'=>'text'])->set("First line\nSecond line");

$f->onSubmit(function ($form) {
    // check what values are submitted
    echo "We're URL encoding submitted values to be able to see what line end is actually submitted.";
    foreach ($form->model->get() as $k=>$v) {
        var_dump([$k => urlencode($v)]);
    }
    echo 'As you can see - without model it submits CRLF, but with model it will normalize all to LF';
});
