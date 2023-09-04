<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Core\Exception as CoreException;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\Js\JsToast;
use Atk4\Ui\Message;
use Atk4\Ui\Modal;
use Atk4\Ui\Tabs;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$tabs = Tabs::addTo($app);

// -----------------------------------------------------------------------------

$tab = $tabs->addTab('Basic Use');

Header::addTo($tab, ['Very simple form']);

$form = Form::addTo($tab);
$form->addControl('email');
$form->onSubmit(static function (Form $form) {
    // implement subscribe here

    return $form->jsSuccess('Subscribed ' . $form->model->get('email') . ' to newsletter.');
});

$form->buttonSave->set('Subscribe');
$form->buttonSave->icon = 'mail';

Header::addTo($tab, ['But very flexible']);

$form = Form::addTo($tab);
$group = $form->addGroup(['width' => 'three']);
$group->addControl('name');
$group->addControl('surname');
$group->addControl('gender', [Form\Control\Dropdown::class, 'values' => ['Female', 'Male']]);

// testing 0 value
$values = ['noob', 'pro', 'dev'];
$form->addControl('description', [Form\Control\Textarea::class])->set(0);
$form->addControl('no_description', [Form\Control\Textarea::class])->set(null);
$form->addControl('status_optional', [Form\Control\Dropdown::class, 'values' => $values]);
$form->addControl('status_string_not-nullable', [Form\Control\Dropdown::class], ['type' => 'string', 'values' => $values, 'nullable' => false]);
$form->addControl('status_integer_not-nullable', [Form\Control\Dropdown::class], ['type' => 'integer', 'values' => $values, 'nullable' => false]);
$form->addControl('status_string_required', [Form\Control\Dropdown::class], ['type' => 'string', 'values' => $values, 'required' => true]);
$form->addControl('status_integer_required', [Form\Control\Dropdown::class], ['type' => 'integer', 'values' => $values, 'required' => true]);

$form->onSubmit(static function (Form $form) use ($app) {
    return new JsToast($app->encodeJson($form->model->get()));
});

Header::addTo($tab, ['Comparing Field type vs Form control class']);
$form = Form::addTo($tab);
$form->addControl('field', [], ['type' => 'date', 'caption' => 'Date using model field:']);
$form->addControl('control', [Form\Control\Calendar::class, 'type' => 'date', 'caption' => 'Date using form control:']);
$form->buttonSave->set('Compare Date');

$form->onSubmit(static function (Form $form) {
    $message = 'field = ' . print_r($form->model->get('field'), true) . '; <br> control = ' . print_r($form->model->get('control'), true);
    $view = new Message('Date field vs control:');
    $view->setApp($form->getApp());
    $view->invokeInit();
    $view->text->addHtml($message);

    return $view;
});

// -----------------------------------------------------------------------------

$tab = $tabs->addTab('Handler Output');

Header::addTo($tab, ['Form can respond with manually generated error']);
$form = Form::addTo($tab);
$form->addControl('email1');
$form->buttonSave->set('Save1');
$form->onSubmit(static function (Form $form) {
    if ($form->getControl('email1')->entityField->get() !== 'pass@bar') {
        return $form->jsError('email1', 'some error action ' . random_int(1, 100));
    }
});

Header::addTo($tab, ['..or success message']);
$form = Form::addTo($tab);
$form->addControl('email2');
$form->buttonSave->set('Save2');
$form->onSubmit(static function (Form $form) {
    return $form->jsSuccess('form was successful');
});

Header::addTo($tab, ['Any other view can be output']);
$form = Form::addTo($tab);
$form->addControl('email3');
$form->buttonSave->set('Save3');
$form->onSubmit(static function (Form $form) {
    $view = new Message('some header');
    $view->setApp($form->getApp());
    $view->invokeInit();
    $view->text->addParagraph('some text ' . random_int(1, 100));

    return $view;
});

Header::addTo($tab, ['Modal can be output directly']);
$form = Form::addTo($tab);
$form->addControl('email4');
$form->buttonSave->set('Save4');
$form->onSubmit(static function (Form $form) {
    $view = new Message('some header');
    $view->setApp($form->getApp());
    $view->invokeInit();
    $view->text->addParagraph('some text ' . random_int(1, 100));

    $modal = new Modal(['title' => 'Something happen', 'ui' => 'modal tiny']);
    $modal->setApp($form->getApp());
    $modal->add($view);

    return $modal;
});

Header::addTo($tab, ['jsAction can be used too']);
$form = Form::addTo($tab);
$control = $form->addControl('email5');
$form->buttonSave->set('Save5');
$form->onSubmit(static function (Form $form) use ($control) {
    return $control->jsInput()->val('random is ' . random_int(1, 100));
});

// -----------------------------------------------------------------------------

$tab = $tabs->addTab('Handler Safety');

Header::addTo($tab, ['Form handles errors', 'size' => 2]);

$form = Form::addTo($tab);
$form->addControl('email');
$form->buttonSave->set('SaveE1');
$form->onSubmit(static function (Form $form) {
    $o = new \stdClass();

    return $o['abc'];
});

Header::addTo($tab, ['Form shows Agile exceptions', 'size' => 2]);

$form = Form::addTo($tab);
$form->addControl('email');
$form->buttonSave->set('SaveE2');
$form->onSubmit(static function (Form $form) {
    throw (new CoreException('Test exception I.'))
        ->addMoreInfo('arg1', 'val1');

    // return 'somehow it did not crash';
});

Button::addTo($form, ['Modal Test', 'class.secondary' => true])
    ->on('click', Modal::addTo($form)->set(static function (View $p) {
        $form = Form::addTo($p);
        $form->name = 'mf';
        $form->addControl('email');
        $form->onSubmit(static function (Form $form) {
            throw (new CoreException('Test exception II.'))
                ->addMoreInfo('arg1', 'val1');

            // return 'somehow it did not crash';
        });
    })->jsShow());

// -----------------------------------------------------------------------------

$tab = $tabs->addTab('Complex Examples');

Header::addTo($tab, ['Conditional response']);

$modelRegister = new Model(new Persistence\Array_());
$modelRegister->addField('name');
$modelRegister->addField('email');
$modelRegister->addField('is_accept_terms', ['type' => 'boolean', 'nullable' => false]);
$modelRegister = $modelRegister->createEntity();

$form = Form::addTo($tab, ['class.segment' => true]);
$form->setModel($modelRegister);

$form->onSubmit(static function (Form $form) {
    if ($form->model->get('name') !== 'John') {
        return $form->jsError('name', 'Your name is not John! It is "' . $form->model->get('name') . '". It should be John. Pleeease!');
    }

    return new JsBlock([
        $form->jsInput('email')->val('john@gmail.com'),
        $form->getControl('is_accept_terms')->js()->checkbox('set checked'),
    ]);
});

// -----------------------------------------------------------------------------

$tab = $tabs->addTab('Layout Control');

Header::addTo($tab, ['Shows example of grouping and multiple errors']);

$form = Form::addTo($tab, ['class.segment' => true]);
$form->setModel((new Model())->createEntity());

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

$form->onSubmit(static function (Form $form) {
    $errors = [];
    foreach ($form->model->getFields() as $name => $ff) {
        if ($name === 'id') {
            continue;
        }

        if ($form->model->get($name) !== 'a') {
            $errors[] = $form->jsError($name, 'Field ' . $name . ' should contain exactly "a", but contains ' . $form->model->get($name));
        }
    }

    return $errors !== [] ? new JsBlock($errors) : $form->jsSuccess('No more errors', 'so we have saved everything into the database');
});
