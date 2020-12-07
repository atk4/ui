<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;

/** @var \Atk4\Ui\App $app */
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
$tabs = \Atk4\Ui\Tabs::addTo($app);

////////////////////////////////////////////
$tab = $tabs->addTab('Basic Use');

\Atk4\Ui\Header::addTo($tab, ['Very simple form']);

$form = Form::addTo($tab);
$form->addControl('email');
$form->onSubmit(function (Form $form) {
    // implement subscribe here

    return $form->success('Subscribed ' . $form->model->get('email') . ' to newsletter.');
});

$form->buttonSave->set('Subscribe');
$form->buttonSave->icon = 'mail';

\Atk4\Ui\Header::addTo($tab, ['But very flexible']);

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

$form->onSubmit(function (Form $form) use ($app) {
    return new \Atk4\Ui\JsToast($app->encodeJson($form->model->get()));
});

\Atk4\Ui\Header::addTo($tab, ['Comparing Field type vs Form control class']);
$form = Form::addTo($tab);
$form->addControl('field', null, ['type' => 'date', 'caption' => 'Date using model field:']);
$form->addControl('control', [Form\Control\Calendar::class, 'type' => 'date', 'caption' => 'Date using form control: ']);
$form->buttonSave->set('Compare Date');

$form->onSubmit(function (Form $form) {
    $message = 'field = ' . print_r($form->model->get('field'), true) . '; <br> control = ' . print_r($form->model->get('control'), true);
    $view = new \Atk4\Ui\Message('Date field vs control:');
    $view->invokeInit();
    $view->text->addHTML($message);

    return $view;
});

////////////////////////////////////////////////////////////
$tab = $tabs->addTab('Handler Output');

\Atk4\Ui\Header::addTo($tab, ['Form can respond with manually generated error']);
$form = Form::addTo($tab);
$form->addControl('email1');
$form->buttonSave->set('Save1');
$form->onSubmit(function (Form $form) {
    return $form->error('email1', 'some error action ' . random_int(1, 100));
});

\Atk4\Ui\Header::addTo($tab, ['..or success message']);
$form = Form::addTo($tab);
$form->addControl('email2');
$form->buttonSave->set('Save2');
$form->onSubmit(function (Form $form) {
    return $form->success('form was successful');
});

\Atk4\Ui\Header::addTo($tab, ['Any other view can be output']);
$form = Form::addTo($tab);
$form->addControl('email3');
$form->buttonSave->set('Save3');
$form->onSubmit(function (Form $form) {
    $view = new \Atk4\Ui\Message('some header');
    $view->invokeInit();
    $view->text->addParagraph('some text ' . random_int(1, 100));

    return $view;
});

\Atk4\Ui\Header::addTo($tab, ['Modal can be output directly']);
$form = Form::addTo($tab);
$form->addControl('email4');
$form->buttonSave->set('Save4');
$form->onSubmit(function (Form $form) {
    $view = new \Atk4\Ui\Message('some header');
    $view->invokeInit();
    $view->text->addParagraph('some text ' . random_int(1, 100));

    $modal = new \Atk4\Ui\Modal(['title' => 'Something happen', 'ui' => 'ui modal tiny']);
    $modal->add($view);

    return $modal;
});

\Atk4\Ui\Header::addTo($tab, ['jsAction can be used too']);
$form = Form::addTo($tab);
$control = $form->addControl('email5');
$form->buttonSave->set('Save5');
$form->onSubmit(function (Form $form) use ($control) {
    return $control->jsInput()->val('random is ' . random_int(1, 100));
});

/////////////////////////////////////////////////////////////////////
$tab = $tabs->addTab('Handler Safety');

\Atk4\Ui\Header::addTo($tab, ['Form handles errors (PHP 7.0+)', 'size' => 2]);

$form = Form::addTo($tab);
$form->addControl('email');
$form->onSubmit(function (Form $form) {
    $o = new \StdClass();

    return $o['abc'];
});

\Atk4\Ui\Header::addTo($tab, ['Form shows Agile exceptions', 'size' => 2]);

$form = Form::addTo($tab);
$form->addControl('email');
$form->onSubmit(function (Form $form) {
    throw (new \Atk4\Core\Exception('testing'))
        ->addMoreInfo('arg1', 'val1');

    return 'somehow it did not crash';
});

\Atk4\Ui\Button::addTo($form, ['Modal Test', 'secondary'])->on('click', \Atk4\Ui\Modal::addTo($form)
    ->set(function ($p) {
        $form = Form::addTo($p);
        $form->addControl('email');
        $form->onSubmit(function (Form $form) {
            throw (new \Atk4\Core\Exception('testing'))
                ->addMoreInfo('arg1', 'val1');

            return 'somehow it did not crash';
        });
    })->show());

/////////////////////////////////////////////////////////////////////
$tab = $tabs->addTab('Complex Examples');

\Atk4\Ui\Header::addTo($tab, ['Conditional response']);

$modelRegister = new \Atk4\Data\Model(new \Atk4\Data\Persistence\Array_());
$modelRegister->addField('name');
$modelRegister->addField('email');
$modelRegister->addField('is_accept_terms', ['type' => 'boolean', 'mandatory' => true]);

$form = Form::addTo($tab, ['segment' => true]);
$form->setModel($modelRegister);

$form->onSubmit(function (Form $form) {
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

\Atk4\Ui\Header::addTo($tab, ['Shows example of grouping and multiple errors']);

$form = Form::addTo($tab, ['segment']);
$form->setModel(new \Atk4\Data\Model());

$form->addHeader('Example fields added one-by-one');
$form->addControl('name');
$form->addControl('email');

$form->addHeader('Example of field grouping');
$group = $form->addGroup('Address with label');
$group->addControl('address', ['width' => 'twelve']);
$group->addControl('code', ['width' => 'four'], ['caption' => 'Post Code']);

$group = $form->addGroup(['width' => 'two']);
$group->addControl('city');
$group->addControl('country');

$group = $form->addGroup(['Name', 'inline' => true]);
$group->addControl('first_name', ['width' => 'eight']);
$group->addControl('middle_name', ['width' => 'three', 'disabled' => true]);
$group->addControl('last_name', ['width' => 'five']);

$form->onSubmit(function (Form $form) {
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
