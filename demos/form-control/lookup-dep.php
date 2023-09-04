<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Form;
use Atk4\Ui\Header;
use Atk4\Ui\Label;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Lookup dependency']);

$form = Form::addTo($app, ['class.segment' => true]);
Label::addTo($form, ['Input information here', 'class.top attached' => true], ['AboveControls']);

$form->addControl('starts_with', [
    Form\Control\Dropdown::class,
    'values' => [
        'a' => 'Letter A',
        'b' => 'Letter B',
        'c' => 'Letter C',
    ],
    'multiple' => true,
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
    'dependency' => static function (Country $model, $data) {
        foreach (explode(',', $data['starts_with'] ?? '') as $letter) {
            $model->addCondition($model->fieldName()->name, 'like', $letter . '%');
        }

        if (isset($data['contains'])) {
            $model->addCondition($model->fieldName()->name, 'like', '%' . $data['contains'] . '%');
        }
    },
    'placeholder' => 'Selection depends on Dropdown above',
    'search' => [
        Country::hinting()->fieldName()->name,
        Country::hinting()->fieldName()->iso,
        Country::hinting()->fieldName()->iso3,
    ],
]);

$form->onSubmit(static function (Form $form) {
    return 'Submitted: ' . print_r($form->model->get(), true);
});

Header::addTo($app, ['Lookup multiple values']);

$form = Form::addTo($app, ['class.segment' => true]);
Label::addTo($form, ['Input information here', 'class.top attached' => true], ['AboveControls']);

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
    'dependency' => static function (Country $model, $data) {
        if (isset($data['ends_with'])) {
            $model->addCondition($model->fieldName()->name, 'like', '%' . $data['ends_with']);
        }
    },
    'multiple' => true,
    'search' => [
        Country::hinting()->fieldName()->name,
        Country::hinting()->fieldName()->iso,
        Country::hinting()->fieldName()->iso3,
    ],
]);

$form->onSubmit(static function (Form $form) {
    return 'Submitted: ' . print_r($form->model->get(), true);
});
