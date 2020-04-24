<?php


chdir('..');
require_once dirname(__DIR__ ) . '/atk-init.php';
require_once dirname(__DIR__ ) . '/_includes/demo-lookup.php';

// create header
\atk4\ui\Header::addTo($app, ['Database-driven form with an enjoyable layout']);

\atk4\ui\FormField\Lookup::addTo($app, ['placeholder' => 'Search country', 'label' => 'Country: '])->setModel(new Country($app->db));

// create form
$form = \atk4\ui\Form::addTo($app, ['segment']);
\atk4\ui\Label::addTo($form, ['Input new country information here', 'top attached'], ['AboveFields']);

$m = new \atk4\data\Model($db, 'test');

// Without Lookup
$m->hasOne('country1', new Country());

// With Lookup
$m->hasOne('country2', [new Country(), 'ui' => ['form' => [
    $demoLookup,  // Special Lookup field that can't save data.
    'plus' => true,
]]]);

$form->setModel($m);

$form->addField('country3', [
    'Lookup',
    'model'       => new Country($db),
    'placeholder' => 'Search for country by code, LV or UK',
    'search'      => ['name', 'iso', 'iso3'],
]);

$form->onSubmit(function ($f) use ($db) {
    $str = $f->model->ref('country1')['name'] . ' ' . $f->model->ref('country2')['name'] . ' ' . (new Country($db))->tryLoad($f->model['country3'])->get('name');
    $view = new \atk4\ui\Message('Select:'); // need in behat test.
    $view->init();
    $view->text->addParagraph($str);

    return $view;
});

\atk4\ui\Header::addTo($app, ['Labels']);

// from seed
\atk4\ui\FormField\Lookup::addTo($app, ['placeholder' => 'Search country', 'label' => 'Country: '])->setModel(new Country($app->db));

// through constructor
\atk4\ui\FormField\Lookup::addTo($app, ['placeholder' => 'Weight', 'labelRight' => new \atk4\ui\Label(['kg', 'basic'])]);
\atk4\ui\FormField\Lookup::addTo($app, ['label' => '$', 'labelRight' => new \atk4\ui\Label(['.00', 'basic'])]);

\atk4\ui\FormField\Lookup::addTo($app, [
    'iconLeft'   => 'tags',
    'labelRight' => new \atk4\ui\Label(['Add Tag', 'tag']),
]);

// left/right corner is not supported, but here is work-around:
$label = new \atk4\ui\Label();
$label->addClass('left corner');
\atk4\ui\Icon::addTo($label, ['asterisk']);

\atk4\ui\FormField\Lookup::addTo($app, [
    'label' => $label,
])->addClass('left corner');

\atk4\ui\Header::addTo($app, ['Auto-complete inside modal']);

$modal = \atk4\ui\Modal::addTo($app)->set(function ($p) {
    $a = \atk4\ui\FormField\Lookup::addTo($p, ['placeholder' => 'Search country', 'label' => 'Country: ']);
    $a->setModel(new Country($p->app->db));
});
\atk4\ui\Button::addTo($app, ['Open Lookup on a Modal window'])->on('click', $modal->show());
