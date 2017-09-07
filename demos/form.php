<?php
/**
 * Apart from demonstrating the form, this example uses an alternative way of rendering the layouts.
 * Here we don't create application object explicitly, instead we use our custom template
 * with a generic layout.
 *
 * We then render everything recursively (renderAll) and plug accumulated JavaScript inside the <head> tag,
 * echoing results after.
 *
 * This approach will also prevent your application from registering shutdown handler or catching error,
 * so we will need to do a bit of work about that too.
 */
require 'init.php';

$tabs = $app->add('Tabs');

////////////////////////////////////////////
$tab = $tabs->addTab('Basic Use');

$tab->add(['Header', 'Very simple form']);

$form = $tab->add('Form');
$form->addField('email');
$form->onSubmit(function($form) {
    // implement subscribe here

    return $form->success("Subscribed ".$form->model['email']." to newsletter.");
});

$form->buttonSave->set('Subscribe');
$form->buttonSave->icon = 'mail';


$tab->add(['Header', 'But very flexible']);

$form = $tab->add('Form');
$g = $form->addGroup(['width'=>'three']);
$g->addField('name');
$g->addField('surname');
$g->addField('gender', ['Dropdown', 'values'=>['Female', 'Male']]);


$tab->add(['Header', 'Comparing Field type vs Decorator class']);
$form = $app->add('Form');
$form->addField('date1', null, ['type'=>'date']);
$form->addField('date2', ['Calendar', 'type'=>'date']);

$form->onSubmit(function($form) {
    echo 'date1 = '.print_r($form->model['date1'], true).' and date2 = '.print_r($form->model['date2'], true);
});



////////////////////////////////////////////////////////////
$tab = $tabs->addTab('Handler Output');

$tab->add(['Header', 'Form can respond with manually generated error']);
$form = $tab->add('Form');
$form->addField('email');
$form->onSubmit(function($form) {
    return $form->error('email', 'some error action '.rand(1,100));
});


$tab->add(['Header', '..or success message']);
$form = $tab->add('Form');
$form->addField('email');
$form->onSubmit(function($form) {
    return $form->success('form was successful');
});

$tab->add(['Header', 'Any other view can be output']);
$form = $tab->add('Form');
$form->addField('email');
$form->onSubmit(function($form) {
    $view = new \atk4\ui\Message('some header');
    $view->init();
    $view->text->addParagraph('some text '.rand(1,100));
    return $view;
});

$tab->add(['Header', 'jsAction can be used too']);
$form = $tab->add('Form');
$field = $form->addField('email');
$form->onSubmit(function($form) use($field) {
    return $field->jsInput()->val('random is '.rand(1,100));
});

/////////////////////////////////////////////////////////////////////
$tab = $tabs->addTab('Handler Safety');

$tab->add(['Header', 'Form handles errors (PHP 7.0+)', 'size'=>2]);

$form = $tab->add('Form');
$form->addField('email');
$form->onSubmit(function($form) {
    $o = new \StdClass();
    return $o['abc'];
});

$tab->add(['Header', 'Form handles random output', 'size'=>2]);

$form = $tab->add('Form');
$form->addField('email');
$form->onSubmit(function($form) {
    echo 'some output here';
});

$tab->add(['Header', 'Form shows Agile exceptions', 'size'=>2]);

$form = $tab->add('Form');
$form->addField('email');
$form->onSubmit(function($form) {
    $form->factory([]);
});


/////////////////////////////////////////////////////////////////////
$tab = $tabs->addTab('Complex Examples');

$tab->add(['Header', 'Conditional response']);

$a = [];
$m_register = new \atk4\data\Model(new \atk4\data\Persistence_Array($a));
$m_register->addField('name');
$m_register->addField('email');
$m_register->addField('is_accept_terms', ['type'=>'boolean', 'mandatory'=>true]);

$f = $tab->add(new \atk4\ui\Form(['segment'=>true]));
$f->setModel($m_register);

$f->onSubmit(function ($f) {
    if ($f->model['name'] != 'John') {
        return $f->error('name', 'Your name is not John! It is "'.$f->model['name'].'". It should be John. Pleeease!');
    } else {
        return [
            $f->jsInput('email')->val('john@gmail.com'),
            $f->jsField('is_accept_terms')->checkbox('set checked'),
        ];
    }
});

////////////////////////////////////////
$tab = $tabs->addTab('Layout Control');

$tab->add(new \atk4\ui\Header(['Shows example of grouping and multiple errors']));

$f = $tab->add(new \atk4\ui\Form(['segment']));
$f->setModel(new \atk4\data\Model());

$f->addHeader('Example fields added one-by-one');
$f->addField('name');
$f->addField('email');

$f->addHeader('Example of field grouping');
$gr = $f->addGroup('Address with label');
$gr->addField('address', ['width'=>'twelve']);
$gr->addField('code', ['width'=>'four'], ['caption'=>'Post Code']);

$gr = $f->addGroup(['width'=>'two']);
$gr->addField('city');
$gr->addField('country');

$gr = $f->addGroup(['Name', 'inline'=>true]);
$gr->addField('first_name', ['width'=>'eight']);
$gr->addField('middle_name', ['width'=>'three', 'disabled'=>true]);
$gr->addField('last_name', ['width'=>'five']);

$f->onSubmit(function ($f) {
    $errors = [];

    foreach ($f->model->elements as $name=>$ff) {
        if ($name == 'id') {
            continue;
        }

        if ($f->model[$name] != 'a') {
            $errors[] = $f->error($name, 'Field '.$name.' should contain exactly "a", but contains '.$f->model[$name]);
        }
    }

    return $errors ?: $f->success('No more errors', 'so we have saved everything into the database');
});

$tabs->addTabURL('Form Database', ['form2.php', 'layout'=>'Centered']);

