<?php

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

// create header
\atk4\ui\Header::addTo($app, ['Database-driven form with an enjoyable layout']);

\atk4\ui\FormField\AutoComplete::addTo($app, ['placeholder' => 'Search users', 'label' => 'http://'])->setModel(new Country($app->db));

// create form
$form = \atk4\ui\Form::addTo($app, ['segment']);
\atk4\ui\Label::addTo($form, ['Input new country information here', 'top attached'], ['AboveFields']);

$m = new \atk4\data\Model($db, 'test');

// Without AutoComplete
$m->hasOne('country1', new Country());

// With AutoComplete
$m->hasOne('country2', [new Country(), 'ui' => ['form' => [
    'AutoComplete',
    'plus' => true,
]]]);

$form->setModel($m);

$form->addField('country3', [
    'AutoComplete',
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
\atk4\ui\FormField\AutoComplete::addTo($app, ['placeholder' => 'Search users', 'label' => 'http://'])->setModel(new Country($app->db));

// through constructor
\atk4\ui\FormField\AutoComplete::addTo($app, ['placeholder' => 'Weight', 'labelRight' => new \atk4\ui\Label(['kg', 'basic'])]);
\atk4\ui\FormField\AutoComplete::addTo($app, ['label' => '$', 'labelRight' => new \atk4\ui\Label(['.00', 'basic'])]);

\atk4\ui\FormField\AutoComplete::addTo($app, [
    'iconLeft'   => 'tags',
    'labelRight' => new \atk4\ui\Label(['Add Tag', 'tag']),
]);

// left/right corner is not supported, but here is work-around:
$label = new \atk4\ui\Label();
$label->addClass('left corner');
\atk4\ui\Icon::addTo($label, ['asterisk']);

\atk4\ui\FormField\AutoComplete::addTo($app, [
    'label' => $label,
])->addClass('left corner');

\atk4\ui\Header::addTo($app, ['Auto-complete inside modal']);

$modal = \atk4\ui\Modal::addTo($app)->set(function ($p) {
    $a = \atk4\ui\FormField\AutoComplete::addTo($p, ['placeholder' => 'Search users', 'label' => 'http://']);
    $a->setModel(new Country($p->app->db));
});
\atk4\ui\Button::addTo($app, ['Open autocomplete on a Modal window'])->on('click', $modal->show());

\atk4\ui\Header::addTo($app, ['New Lookup field']);

$form = \atk4\ui\Form::addTo($app, ['segment']);
\atk4\ui\Label::addTo($form, ['Input new country information here', 'top attached'], ['AboveFields']);

$c = new Country($db);
$c->addExpression('letter1', 'concat("Ends with ", substring([name], -1))');

$form->addField('country_a', [
    'Lookup',
    'model'       => new Country($db),
    'hint'        => 'Lookup field is just like AutoComplete, supports all the same options.',
    'placeholder' => 'Search for country by code, LV or UK',
    'search'      => ['name', 'iso', 'iso3'],
]);

$lookup = $form->addField('country_b', [
    'Lookup',
    'model'       => $c,
    'hint'        => 'However one or few "filtering" options can be added narrowing down the final result set',
    'placeholder' => 'Search for country by code, LV or UK',
    'search'      => ['name', 'iso', 'iso3'],
]);
$lookup->addFilter('letter1');

$form->buttonSave->set('Add Countries');

\atk4\ui\Header::addTo($app, ['Auto-complete dependency']);

$form = \atk4\ui\Form::addTo($app, ['segment']);
\atk4\ui\Label::addTo($form, ['Input information here', 'top attached'], ['AboveFields']);

$form->addField('starts_with', [
    'DropDown',
    'values'       => [
        'a' => 'Letter A',
        'b' => 'Letter B',
        'c' => 'Letter C',
    ],
    'isMultiple'  => true,
    'hint'        => 'Select start letter that auto-complete selection of Country will depend on.',
    'placeholder' => 'Search for country starting with ...',
]);

$form->addField('contains', [
    'Line',
    'hint'        => 'Select string that auto-complete selection of Country will depend on.',
    'placeholder' => 'Search for country containing ...',
]);

$lookup = $form->addField('country', [
    'AutoComplete',
    'model'       => new Country($db),
    'dependency'  => function ($model, $data) {
        $conditions = [];
        foreach (explode(',', $data['starts_with'] ?? '') as $letter) {
            $conditions[] = ['name', 'like', $letter . '%'];
        }

        if ($conditions) {
            $model->addCondition($conditions);
        }

        isset($data['contains']) ? $model->addCondition('name', 'like', '%' . $data['contains'] . '%') : null;
    },
    'placeholder' => 'Selection depends on DropDown above',
    'search'      => ['name', 'iso', 'iso3'],
]);

$form->onSubmit(function ($form) {
    return 'Submitted: ' . print_r($form->model->get(), true);
});

\atk4\ui\Header::addTo($app, ['Auto-complete multiple values']);

$form = \atk4\ui\Form::addTo($app, ['segment']);
\atk4\ui\Label::addTo($form, ['Input information here', 'top attached'], ['AboveFields']);

$form->addField('ends_with', [
    'DropDown',
    'values'       => [
        'a' => 'Letter A',
        'b' => 'Letter B',
        'c' => 'Letter C',
    ],
    'hint'        => 'Select end letter that auto-complete selection of Country will depend on.',
    'placeholder' => 'Search for country ending with ...',
]);

$lookup = $form->addField('country', [
    'AutoComplete',
    'model'       => new Country($db),
    'dependency'  => function ($model, $data) {
        isset($data['ends_with']) ? $model->addCondition('name', 'like', '%' . $data['ends_with']) : null;
    },
    'multiple'    => true,
    'search'      => ['name', 'iso', 'iso3'],
]);

$form->onSubmit(function ($form) {
    return 'Submitted: ' . print_r($form->model->get(), true);
});
