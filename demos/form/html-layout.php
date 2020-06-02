<?php

use atk4\ui\Form;
use atk4\ui\FormLayout\Custom;
use atk4\ui\FormLayout\Generic;
use atk4\ui\GridLayout;
use atk4\ui\Header;
use atk4\ui\Tabs;
use atk4\ui\View;

require_once __DIR__ . '/../atk-init.php';
require_once __DIR__ . '/../_includes/flyers-form-demo.php';

$data = [];

Header::addTo($app, ['Display form using Html template', 'subHeader' => 'Fully control how to display fields.']);

$tabs = Tabs::addTo($app);

$tab = $tabs->addTab('Layout using field name');

$f = FlyersForm::addTo($tab, ['db' => $db, 'layout' => [
    Generic::class, ['defaultTemplate' => __DIR__ . '/templates/flyers-form-layout.html'],
],
]);

////////////////////////////////////////
$tab = $tabs->addTab('Template samples');

$g_l = GridLayout::addTo($tab, ['rows' => 1, 'columns' => 2])->addClass('internally celled');

$right = View::addTo($g_l, [], ['r1c1']);
Header::addTo($right, ['Button on right']);

$form = Form::addTo($right, ['layout' => [Generic::class, 'defaultTemplate' => __DIR__ . '/templates/form-button-right.html']]);
$form->setModel(new Flyers(new \atk4\data\Persistence\Array_($data)));
$form->getField('last_name')->hint = 'Please enter your last name.';

$left = View::addTo($g_l, [], ['r1c2']);
Header::addTo($left, ['Hint placement']);

$form = Form::addTo($left, [
    'layout' => [
        Generic::class,
        ['defaultInputTemplate' => __DIR__ . '/templates/input.html',
            'defaultHint' => [\atk4\ui\Label::class, 'class' => ['pointing', 'below']],
        ],
    ],
]);
$form->setModel(new Flyers(new \atk4\data\Persistence\Array_($data)));
$form->getField('last_name')->hint = 'Please enter your last name.';

////////////////////////////////////////
$tab = $tabs->addTab('Custom layout class');

$form = Form::addTo($tab, ['layout' => [Custom::class, 'defaultTemplate' => __DIR__ . '/templates/form-custom-layout.html']]);
$form->setModel(new \atk4\ui\demo\CountryLock($db))->loadAny();

$form->onSubmit(function ($f) {
    return new \atk4\ui\jsToast('Saving is disabled');
});
