<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\Form;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

// create header
\atk4\ui\Header::addTo($app, ['Lookup Input']);

Form\Control\Lookup::addTo($app, ['placeholder' => 'Search country', 'label' => 'Country: '])->setModel(new Country($app->db));

// create form
$form = Form::addTo($app, ['segment']);
\atk4\ui\Label::addTo($form, ['Lookup countries', 'top attached'], ['AboveControls']);

$model = new \atk4\data\Model($app->db, 'test');

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
    $str = $form->model->ref('country1')->get('name') . ' ' . $form->model->ref('country2')->get('name') . ' ' . (new Country($form->app->db))->tryLoad($form->model->get('country3'))->get('name');
    $view = new \atk4\ui\Message('Select:'); // need in behat test.
    $view->invokeInit();
    $view->text->addParagraph($str);

    return $view;
});

\atk4\ui\Header::addTo($app, ['Lookup input using label']);

// from seed
Form\Control\Lookup::addTo($app, ['placeholder' => 'Search country', 'label' => 'Country: '])->setModel(new Country($app->db));

// through constructor
Form\Control\Lookup::addTo($app, ['placeholder' => 'Weight', 'labelRight' => new \atk4\ui\Label(['kg', 'basic'])]);
Form\Control\Lookup::addTo($app, ['label' => '$', 'labelRight' => new \atk4\ui\Label(['.00', 'basic'])]);

Form\Control\Lookup::addTo($app, [
    'iconLeft' => 'tags',
    'labelRight' => new \atk4\ui\Label(['Add Tag', 'tag']),
]);

// left/right corner is not supported, but here is work-around:
$label = new \atk4\ui\Label();
$label->addClass('left corner');
\atk4\ui\Icon::addTo($label, ['asterisk']);

Form\Control\Lookup::addTo($app, [
    'label' => $label,
])->addClass('left corner');

\atk4\ui\Header::addTo($app, ['Lookup input inside modal']);

$modal = \atk4\ui\Modal::addTo($app)->set(function ($p) {
    $a = Form\Control\Lookup::addTo($p, ['placeholder' => 'Search country', 'label' => 'Country: ']);
    $a->setModel(new Country($p->app->db));
});
\atk4\ui\Button::addTo($app, ['Open Lookup on a Modal window'])->on('click', $modal->show());
