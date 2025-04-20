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

$makeTestStringFx = static fn ($v) => $v . ' <b>"\' &lt;&quot;&amp;';
$htmlValues = [
    $makeTestStringFx('d') => $makeTestStringFx('dTitle'),
    $makeTestStringFx('u') => $makeTestStringFx('uTitle'),
    $makeTestStringFx('v') => $makeTestStringFx('vTitle'),
    '[0 ]' => '[ ""]', // https://github.com/atk4/ui/pull/2274
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

$form->addControl('dropdown_multi_json', [
    Form\Control\Dropdown::class,
    'caption' => 'Dropdown multiple JSON',
    'multiple' => true,
    'values' => $htmlValues,
], ['type' => 'json']);

$lookupModel = new Model();
$lookupModel->addField('id', ['type' => 'string']);
$lookupModel->addField('name', ['type' => 'string']);
$lookupModel->setPersistence(new Persistence\Array_(array_combine(
    array_keys($htmlValues),
    array_map(static fn ($v) => ['name' => $v], $htmlValues)
)));

$form->addControl('lookup_single', [
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
]);

$form->addControl('lookup_multi_json', [
    Form\Control\Lookup::class,
    'caption' => 'Lookup multiple JSON',
    'multiple' => true,
    'model' => $lookupModel,
]/* , ['type' => 'json'] */);
$form->entity->getField('lookup_multi_json')->type = 'json';

foreach (array_keys($form->entity->getFields()) as $k) {
    $form->entity->set(
        $k,
        str_contains($k, 'json')
            ? [$makeTestStringFx('d'), $makeTestStringFx('v')]
            : $makeTestStringFx('d') . (str_contains($k, 'multi') ? ',' . $makeTestStringFx('v') : '')
    );
}

$initData = $form->entity->get();

$form->onSubmit(static function (Form $form) use ($app, $initData, $makeTestStringFx) {
    $makeExpectedDataFx = static fn ($fx) => array_map(static function ($k) use ($fx) {
        $res = $fx($k);

        return str_contains($k, 'json')
            ? explode(',', $res)
            : $res;
    }, array_combine(array_keys($initData), array_keys($initData)));

    $view = new Message('Values:');
    $view->setApp($form->getApp());
    $view->invokeInit();
    $view->text->addParagraph($app->encodeJson($form->entity->get()));
    $view->text->addParagraph('match init: ' . ($form->entity->get() === $initData));
    $view->text->addParagraph('match u add: ' . ($form->entity->get() === $makeExpectedDataFx(static fn ($k) => (str_contains($k, 'multi') ? $makeTestStringFx('d') . ',' . $makeTestStringFx('v') . ',' : '') . $makeTestStringFx('u'))));
    $view->text->addParagraph('match empty: ' . ($form->entity->get() === $makeExpectedDataFx(static fn () => '')));
    $view->text->addParagraph('match u only: ' . ($form->entity->get() === $makeExpectedDataFx(static fn () => $makeTestStringFx('u'))));
    $view->text->addParagraph('match json-like add: ' . ($form->entity->get() === $makeExpectedDataFx(static fn ($k) => (str_contains($k, 'multi') ? $makeTestStringFx('u') . ',' : '') . '[0 ]')));

    return $view;
});
