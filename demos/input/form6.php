<?php

namespace atk4\ui\demo;

require_once __DIR__ . '/../atk-init.php';

\atk4\ui\View::addTo($app, [
    'Forms below demonstrate how to work with multi-value selectors',
    'ui' => 'ignored warning message',
]);

$cc = \atk4\ui\Columns::addTo($app);
$f = \atk4\ui\Form::addTo($cc->addColumn());

$f->addField('one', null, ['enum' => ['female', 'male']])->set('male');
$f->addField('two', [\atk4\ui\FormField\Radio::class], ['enum' => ['female', 'male']])->set('male');

$f->addField('three', null, ['values' => ['female', 'male']])->set(1);
$f->addField('four', [\atk4\ui\FormField\Radio::class], ['values' => ['female', 'male']])->set(1);

$f->addField('five', null, ['values' => [5 => 'female', 7 => 'male']])->set(7);
$f->addField('six', [\atk4\ui\FormField\Radio::class], ['values' => [5 => 'female', 7 => 'male']])->set(7);

$f->addField('seven', null, ['values' => ['F' => 'female', 'M' => 'male']])->set('M');
$f->addField('eight', [\atk4\ui\FormField\Radio::class], ['values' => ['F' => 'female', 'M' => 'male']])->set('M');

$f->onSubmit(function (\atk4\ui\Form $form) {
    echo json_encode($form->model->get());
});
