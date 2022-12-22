<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Ui\Button;
use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Icon;
use Atk4\Ui\Label;
use Atk4\Ui\Message;
use Atk4\Ui\Modal;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// create header
Header::addTo($app, ['Lookup Input']);

Form\Control\Lookup::addTo($app, ['placeholder' => 'Search country', 'label' => 'Country: '])
    ->setModel(new Country($app->db));

// create form
$form = Form::addTo($app, ['class.segment' => true]);
Label::addTo($form, ['Lookup countries', 'class.top attached' => true], ['AboveControls']);

$model = new Model($app->db, ['table' => 'test']);

// Without Lookup
$model->hasOne('country1', ['model' => [Country::class]]);

// With Lookup
$model->hasOne('country2', ['model' => [Country::class], 'ui' => ['form' => [
    DemoLookup::class,
    'plus' => true,
]]]);

$form->setModel($model->createEntity());

$form->addControl('country3', [
    Form\Control\Lookup::class,
    'model' => new Country($app->db),
    'placeholder' => 'Search for country by name or iso value',
    'search' => ['name', 'iso', 'iso3'],
]);

$form->onSubmit(function (Form $form) {
    $view = new Message('Select:'); // need in behat test.
    $view->invokeInit();
    $view->text->addParagraph($form->model->ref('country1')->get(Country::hinting()->fieldName()->name) ?? 'null');
    $view->text->addParagraph($form->model->ref('country2')->get(Country::hinting()->fieldName()->name) ?? 'null');
    $view->text->addParagraph($form->model->get('country3') !== '' // related with https://github.com/atk4/ui/pull/1805
        ? (new Country($form->getApp()->db))->load($form->model->get('country3'))->get(Country::hinting()->fieldName()->name)
        : 'null');

    return $view;
});

Header::addTo($app, ['Lookup input using label']);

// from seed
Form\Control\Lookup::addTo($app, ['placeholder' => 'Search country', 'label' => 'Country: '])
    ->setModel(new Country($app->db));

// through constructor
Form\Control\Lookup::addTo($app, ['placeholder' => 'Weight', 'labelRight' => new Label(['kg', 'class.basic' => true])]);
Form\Control\Lookup::addTo($app, ['label' => '$', 'labelRight' => new Label(['.00', 'class.basic' => true])]);

Form\Control\Lookup::addTo($app, [
    'iconLeft' => 'tags',
    'labelRight' => new Label(['Add Tag', 'class.tag' => true]),
]);

// left/right corner is not supported, but here is work-around:
$label = new Label();
$label->addClass('left corner');
Icon::addTo($label, ['asterisk']);

Form\Control\Lookup::addTo($app, [
    'label' => $label,
])->addClass('left corner');

Header::addTo($app, ['Lookup input inside modal']);

$modal = Modal::addTo($app)->set(function (View $p) {
    $a = Form\Control\Lookup::addTo($p, ['placeholder' => 'Search country', 'label' => 'Country: ']);
    $a->setModel(new Country($p->getApp()->db));
});
Button::addTo($app, ['Open Lookup on a Modal window'])->on('click', $modal->jsShow());
