<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Header::addTo($app, ['Lookup dependency']);

$form = Form::addTo($app, ['segment']);
\Atk4\Ui\Label::addTo($form, ['Input information here', 'top attached'], ['AboveControls']);

$form->addControl('starts_with', [
    Form\Control\Dropdown::class,
    'values' => [
        'a' => 'Letter A',
        'b' => 'Letter B',
        'c' => 'Letter C',
    ],
    'isMultiple' => true,
    'hint' => 'Select start letter that lookup selection of Country will depend on.',
    'placeholder' => 'Search for country starting with ...',
]);

$form->addControl('contains', [
    Form\Control\Line::class,
    'hint' => 'Select string that lookup selection of Country will depend on.',
    'placeholder' => 'Search for country containing ...',
]);

$lookup = $form->addControl('country', [
    Form\Control\Lookup::class,
    'model' => new Country($app->db),
    'dependency' => function ($model, $data) {
        foreach (explode(',', $data['starts_with'] ?? '') as $letter) {
            $model->addCondition('name', 'like', $letter . '%');
        }

        isset($data['contains']) ? $model->addCondition('name', 'like', '%' . $data['contains'] . '%') : null;
    },
    'placeholder' => 'Selection depends on Dropdown above',
    'search' => ['name', 'iso', 'iso3'],
]);

$form->onSubmit(function (Form $form) {
    return 'Submitted: ' . print_r($form->model->get(), true);
});

\Atk4\Ui\Header::addTo($app, ['Lookup multiple values']);

$form = Form::addTo($app, ['segment']);
\Atk4\Ui\Label::addTo($form, ['Input information here', 'top attached'], ['AboveControls']);

$form->addControl('ends_with', [
    Form\Control\Dropdown::class,
    'values' => [
        'a' => 'Letter A',
        'e' => 'Letter E',
        'y' => 'Letter Y',
    ],
    'hint' => 'Select end letter that lookup selection of Country will depend on.',
    'placeholder' => 'Search for country ending with ...',
]);

$lookup = $form->addControl('country', [
    Form\Control\Lookup::class,
    'model' => new Country($app->db),
    'dependency' => function ($model, $data) {
        isset($data['ends_with']) ? $model->addCondition('name', 'like', '%' . $data['ends_with']) : null;
    },
    'multiple' => true,
    'search' => ['name', 'iso', 'iso3'],
]);

$form->onSubmit(function (Form $form) {
    return 'Submitted: ' . print_r($form->model->get(), true);
});
