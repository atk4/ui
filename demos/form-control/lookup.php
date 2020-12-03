<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// create header
\Atk4\Ui\Header::addTo($app, ['Lookup Input']);

Form\Control\Lookup::addTo($app, ['placeholder' => 'Search country', 'label' => 'Country: '])->setModel(new Country($app->db));

// create form
$form = Form::addTo($app, ['segment']);
\Atk4\Ui\Label::addTo($form, ['Lookup countries', 'top attached'], ['AboveControls']);

$model = new \Atk4\Data\Model($app->db, 'test');

// Without Lookup
$model->hasOne('country1', new Country());

// With Lookup
$model->hasOne('country2', [new Country(), 'ui' => ['form' => [
    DemoLookup::class,
    'plus' => true,
]]]);

$form->setModel($model);

$form->addControl('country3', [
    Form\Control\Lookup::class,
    'model' => new Country($app->db),
    'placeholder' => 'Search for country by name or iso value',
    'search' => ['name', 'iso', 'iso3'],
]);

$form->onSubmit(function (Form $form) {
    $str = $form->model->ref('country1')->get('name') . ' ' . $form->model->ref('country2')->get('name') . ' ' . (new Country($form->getApp()->db))->tryLoad($form->model->get('country3'))->get('name');
    $view = new \Atk4\Ui\Message('Select:'); // need in behat test.
    $view->invokeInit();
    $view->text->addParagraph($str);

    return $view;
});

\Atk4\Ui\Header::addTo($app, ['Lookup input using label']);

// from seed
Form\Control\Lookup::addTo($app, ['placeholder' => 'Search country', 'label' => 'Country: '])->setModel(new Country($app->db));

// through constructor
Form\Control\Lookup::addTo($app, ['placeholder' => 'Weight', 'labelRight' => new \Atk4\Ui\Label(['kg', 'basic'])]);
Form\Control\Lookup::addTo($app, ['label' => '$', 'labelRight' => new \Atk4\Ui\Label(['.00', 'basic'])]);

Form\Control\Lookup::addTo($app, [
    'iconLeft' => 'tags',
    'labelRight' => new \Atk4\Ui\Label(['Add Tag', 'tag']),
]);

// left/right corner is not supported, but here is work-around:
$label = new \Atk4\Ui\Label();
$label->addClass('left corner');
\Atk4\Ui\Icon::addTo($label, ['asterisk']);

Form\Control\Lookup::addTo($app, [
    'label' => $label,
])->addClass('left corner');

\Atk4\Ui\Header::addTo($app, ['Lookup input inside modal']);

$modal = \Atk4\Ui\Modal::addTo($app)->set(function ($p) {
    $a = Form\Control\Lookup::addTo($p, ['placeholder' => 'Search country', 'label' => 'Country: ']);
    $a->setModel(new Country($p->getApp()->db));
});
\Atk4\Ui\Button::addTo($app, ['Open Lookup on a Modal window'])->on('click', $modal->show());
