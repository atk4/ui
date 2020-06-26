<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Form;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

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
$tabs = \atk4\ui\Tabs::addTo($app);

////////////////////////////////////////////
$tab = $tabs->addTab('Basic Use');

\atk4\ui\Header::addTo($tab, ['Very simple form']);

$form = Form::addTo($tab);
$form->addControl('email');
$form->onSubmit(function (Form $form) {
    // implement subscribe here

    return $form->success('Subscribed ' . $form->model->get('email') . ' to newsletter.');
});

$form->buttonSave->set('Subscribe');
$form->buttonSave->icon = 'mail';

\atk4\ui\Header::addTo($tab, ['But very flexible']);

$form = Form::addTo($tab);
$group = $form->addGroup(['width' => 'three']);
$group->addControl('name');
$group->addControl('surname');
$group->addControl('gender', [Form\Control\Dropdown::class, 'values' => ['Female', 'Male']]);

// testing 0 value
$values = [0 => 'noob', 1 => 'pro', 2 => 'dev'];
$form->addControl('description', [Form\Control\Textarea::class])->set(0);
$form->addControl('no_description', [Form\Control\Textarea::class])->set(null);
$form->addControl('status_optional', [Form\Control\Dropdown::class, 'values' => $values]);
$form->addControl('status_string_required', [Form\Control\Dropdown::class], ['type' => 'string', 'values' => $values, 'required' => true]);
$form->addControl('status_integer_required', [Form\Control\Dropdown::class], ['type' => 'integer', 'values' => $values, 'required' => true]);
$form->addControl('status_string_mandatory', [Form\Control\Dropdown::class], ['type' => 'string', 'values' => $values, 'mandatory' => true]);
$form->addControl('status_integer_mandatory', [Form\Control\Dropdown::class], ['type' => 'integer', 'values' => $values, 'mandatory' => true]);

$form->onSubmit(function (Form $form) {
    return (new \atk4\ui\jsNotify(json_encode($form->model->get())))->setDuration(0);
});

\atk4\ui\Header::addTo($tab, ['Comparing Field type vs Form control class']);
$form = Form::addTo($tab);
$form->addControl('date1', null, ['type' => 'date']);
$form->addControl('date2', [Form\Control\Calendar::class, 'type' => 'date']);
$form->buttonSave->set('Compare Date');

$form->onSubmit(function (Form $form) {
    echo 'date1 = ' . print_r($form->model->get('date1'), true) . ' and date2 = ' . print_r($form->model->get('date2'), true);
});

////////////////////////////////////////////////////////////
$tab = $tabs->addTab('Handler Output');

\atk4\ui\Header::addTo($tab, ['Form can respond with manually generated error']);
$form = Form::addTo($tab);
$form->addControl('email1');
$form->buttonSave->set('Save1');
$form->onSubmit(function (Form $form) {
    return $form->error('email1', 'some error action ' . random_int(1, 100));
});

\atk4\ui\Header::addTo($tab, ['..or success message']);
$form = Form::addTo($tab);
$form->addControl('email2');
$form->buttonSave->set('Save2');
$form->onSubmit(function (Form $form) {
    return $form->success('form was successful');
});

\atk4\ui\Header::addTo($tab, ['Any other view can be output']);
$form = Form::addTo($tab);
$form->addControl('email3');
$form->buttonSave->set('Save3');
$form->onSubmit(function (Form $form) {
    $view = new \atk4\ui\Message('some header');
    $view->init();
    $view->text->addParagraph('some text ' . random_int(1, 100));

    return $view;
});

\atk4\ui\Header::addTo($tab, ['Modal can be output directly']);
$form = Form::addTo($tab);
$form->addControl('email4');
$form->buttonSave->set('Save4');
$form->onSubmit(function (Form $form) {
    $view = new \atk4\ui\Message('some header');
    $view->init();
    $view->text->addParagraph('some text ' . random_int(1, 100));

    $modal = new \atk4\ui\Modal(['title' => 'Something happen', 'ui' => 'ui modal tiny']);
    $modal->add($view);

    return $modal;
});

\atk4\ui\Header::addTo($tab, ['jsAction can be used too']);
$form = Form::addTo($tab);
$control = $form->addControl('email5');
$form->buttonSave->set('Save5');
$form->onSubmit(function (Form $form) use ($control) {
    return $control->jsInput()->val('random is ' . random_int(1, 100));
});

/////////////////////////////////////////////////////////////////////
$tab = $tabs->addTab('Handler Safety');

\atk4\ui\Header::addTo($tab, ['Form handles errors (PHP 7.0+)', 'size' => 2]);

$form = Form::addTo($tab);
$form->addControl('email');
$form->onSubmit(function (Form $form) {
    $o = new \StdClass();

    return $o['abc'];
});

\atk4\ui\Header::addTo($tab, ['Form handles random output', 'size' => 2]);

$form = Form::addTo($tab);
$form->addControl('email');
$form->onSubmit(function (Form $form) {
    echo 'some output here';
});

\atk4\ui\Header::addTo($tab, ['Form shows Agile exceptions', 'size' => 2]);

$form = Form::addTo($tab);
$form->addControl('email');
$form->onSubmit(function (Form $form) {
    throw (new \atk4\core\Exception('testing'))
        ->addMoreInfo('arg1', 'val1');

    return 'somehow it did not crash';
});

\atk4\ui\Button::addTo($form, ['Modal Test', 'secondary'])->on('click', \atk4\ui\Modal::addTo($form)
    ->set(function ($p) {
        $form = Form::addTo($p);
        $form->addControl('email');
        $form->onSubmit(function (Form $form) {
            throw (new \atk4\core\Exception('testing'))
                ->addMoreInfo('arg1', 'val1');

            return 'somehow it did not crash';
        });
    })->show());

/////////////////////////////////////////////////////////////////////
$tab = $tabs->addTab('Complex Examples');

\atk4\ui\Header::addTo($tab, ['Conditional response']);

$m_register = new \atk4\data\Model(new \atk4\data\Persistence\Array_());
$m_register->addField('name');
$m_register->addField('email');
$m_register->addField('is_accept_terms', ['type' => 'boolean', 'mandatory' => true]);

$f = Form::addTo($tab, ['segment' => true]);
$f->setModel($m_register);

$f->onSubmit(function (Form $form) {
    if ($form->model->get('name') !== 'John') {
        return $form->error('name', 'Your name is not John! It is "' . $form->model->get('name') . '". It should be John. Pleeease!');
    }

    return [
        $form->jsInput('email')->val('john@gmail.com'),
        $form->jsControl('is_accept_terms')->checkbox('set checked'),
    ];
});

////////////////////////////////////////
$tab = $tabs->addTab('Layout Control');

\atk4\ui\Header::addTo($tab, ['Shows example of grouping and multiple errors']);

$f = Form::addTo($tab, ['segment']);
$f->setModel(new \atk4\data\Model());

$f->addHeader('Example fields added one-by-one');
$f->addControl('name');
$f->addControl('email');

$f->addHeader('Example of field grouping');
$gr = $f->addGroup('Address with label');
$gr->addControl('address', ['width' => 'twelve']);
$gr->addControl('code', ['width' => 'four'], ['caption' => 'Post Code']);

$gr = $f->addGroup(['width' => 'two']);
$gr->addControl('city');
$gr->addControl('country');

$gr = $f->addGroup(['Name', 'inline' => true]);
$gr->addControl('first_name', ['width' => 'eight']);
$gr->addControl('middle_name', ['width' => 'three', 'disabled' => true]);
$gr->addControl('last_name', ['width' => 'five']);

$f->onSubmit(function (Form $form) {
    $errors = [];

    foreach ($form->model->getFields() as $name => $ff) {
        if ($name === 'id') {
            continue;
        }

        if ($form->model->get($name) !== 'a') {
            $errors[] = $form->error($name, 'Field ' . $name . ' should contain exactly "a", but contains ' . $form->model->get($name));
        }
    }

    return $errors ?: $form->success('No more errors', 'so we have saved everything into the database');
});
