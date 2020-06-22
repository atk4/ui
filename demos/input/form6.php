<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

use atk4\ui\Form;

\atk4\ui\View::addTo($app, [
    'Forms below demonstrate how to work with multi-value selectors',
    'ui' => 'ignored warning message',
]);

$cc = \atk4\ui\Columns::addTo($app);
$form = Form::addTo($cc->addColumn());

$form->addField('one', null, ['enum' => ['female', 'male']])->set('male');
$form->addField('two', [Form\Field\Radio::class], ['enum' => ['female', 'male']])->set('male');

$form->addField('three', null, ['values' => ['female', 'male']])->set(1);
$form->addField('four', [Form\Field\Radio::class], ['values' => ['female', 'male']])->set(1);

$form->addField('five', null, ['values' => [5 => 'female', 7 => 'male']])->set(7);
$form->addField('six', [Form\Field\Radio::class], ['values' => [5 => 'female', 7 => 'male']])->set(7);

$form->addField('seven', null, ['values' => ['F' => 'female', 'M' => 'male']])->set('M');
$form->addField('eight', [Form\Field\Radio::class], ['values' => ['F' => 'female', 'M' => 'male']])->set('M');

$form->onSubmit(function (Form $form) {
    echo json_encode($form->model->get());
});
