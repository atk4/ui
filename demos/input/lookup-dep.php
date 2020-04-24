<?php

chdir('..');

require_once 'atk-init.php';




\atk4\ui\Header::addTo($app, ['Lookup dependency']);

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
    'hint'        => 'Select start letter that lookup selection of Country will depend on.',
    'placeholder' => 'Search for country starting with ...',
]);

$form->addField('contains', [
    'Line',
    'hint'        => 'Select string that lookup selection of Country will depend on.',
    'placeholder' => 'Search for country containing ...',
]);

$lookup = $form->addField('country', [
    'Lookup',
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

\atk4\ui\Header::addTo($app, ['Lookup multiple values']);

$form = \atk4\ui\Form::addTo($app, ['segment']);
\atk4\ui\Label::addTo($form, ['Input information here', 'top attached'], ['AboveFields']);

$form->addField('ends_with', [
    'DropDown',
    'values'       => [
        'a' => 'Letter A',
        'e' => 'Letter E',
        'y' => 'Letter Y',
    ],
    'hint'        => 'Select end letter that lookup selection of Country will depend on.',
    'placeholder' => 'Search for country ending with ...',
]);

$lookup = $form->addField('country', [
    'Lookup',
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
