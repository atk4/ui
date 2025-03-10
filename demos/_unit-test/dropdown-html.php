<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\App;
use Atk4\Ui\Form;
use Atk4\Ui\Message;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$makeTestStringFx = static fn ($v) => $v . ' <b> &lt;';
$htmlValues = [
    $makeTestStringFx('d') => $makeTestStringFx('dTitle'),
    $makeTestStringFx('u') => $makeTestStringFx('uTitle'),
];

$form = Form::addTo($app);

$form->addControl('dropdown_single', [
    Form\Control\Dropdown::class,
    'caption' => 'Dropdown single',
    'values' => $htmlValues,
]);

$form->addControl('dropdown_single2', [
    Form\Control\Dropdown::class,
    'caption' => 'Dropdown single allow addition',
    'values' => $htmlValues,
    'dropdownOptions' => ['allowAdditions' => true],
]);

$form->addControl('dropdown_multi', [
    Form\Control\Dropdown::class,
    'caption' => 'Dropdown multiple',
    'multiple' => true,
    'values' => $htmlValues,
]);

$form->addControl('dropdown_multi2', [
    Form\Control\Dropdown::class,
    'caption' => 'Dropdown multiple allow addition',
    'multiple' => true,
    'values' => $htmlValues,
    'dropdownOptions' => ['allowAdditions' => true],
]);

$lookupModel = new Model();
$lookupModel->addField('id', ['type' => 'string']);
$lookupModel->addField('name', ['type' => 'string']);
$lookupModel->setPersistence(new Persistence\Array_(array_combine(
    array_keys($htmlValues),
    array_map(static fn ($v) => ['name' => $v], $htmlValues)
)));

/* $form->addControl('lookup_single', [
    Form\Control\Lookup::class,
    'caption' => 'Lookup single',
    'model' => $lookupModel,
]);

$form->addControl('lookup_single2', [
    Form\Control\Lookup::class,
    'caption' => 'Lookup single allow addition',
    'model' => $lookupModel,
    'settings' => ['allowAdditions' => true],
]);

$form->addControl('lookup_multi', [
    Form\Control\Lookup::class,
    'caption' => 'Lookup multiple',
    'multiple' => true,
    'model' => $lookupModel,
]);

$form->addControl('lookup_multi2', [
    Form\Control\Lookup::class,
    'caption' => 'Lookup multiple allow addition',
    'multiple' => true,
    'model' => $lookupModel,
    'settings' => ['allowAdditions' => true],
]); */

foreach (array_keys($form->entity->getFields()) as $k) {
    $form->entity->set($k, $makeTestStringFx('d'));
}

$initData = $form->entity->get();

$form->onSubmit(static function (Form $form) use ($app, $initData, $makeTestStringFx) {
    $message = $app->encodeJson($form->entity->get());

    $view = new Message('Values:');
    $view->setApp($form->getApp());
    $view->invokeInit();
    $view->text->addParagraph($message);
    $view->text->addParagraph('match init: ' . ($form->entity->get() === $initData));
    $view->text->addParagraph('match u add: ' . ($form->entity->get() === array_map(static fn ($k) => (str_contains($k, 'multi') ? $initData[$k] . ',' : '') . $makeTestStringFx('u'), array_combine(array_keys($initData), array_keys($initData)))));
    $view->text->addParagraph('match empty: ' . ($form->entity->get() === array_map(static fn () => '', $initData)));
    $view->text->addParagraph('match u only: ' . ($form->entity->get() === array_map(static fn () => $makeTestStringFx('u'), $initData)));

    return $view;
});
