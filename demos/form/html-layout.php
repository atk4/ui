<?php

require_once __DIR__ . '/../atk-init.php';
require_once __DIR__ . '/../_includes/flyers-form-demo.php';

\atk4\ui\Header::addTo($app, ['Display form using Html template', 'subHeader' => 'Fully control how to display fields.']);

$tabs = \atk4\ui\Tabs::addTo($app);

$tab = $tabs->addTab('Layout using field name');

$f = FlyersForm::addTo($tab, ['db' => $db, 'layout' => ['Generic', 'defaultTemplate' => __DIR__ . '/templates/flyers-form-layout.html']]);

$tab = $tabs->addTab('Custom layout class');

$form = \atk4\ui\Form::addTo($tab, ['layout' => ['Custom', 'defaultTemplate' => __DIR__ . '/templates/form-custom-layout.html']]);
$form->setModel(new CountryLock($db))->loadAny();

$form->onSubmit(function ($f) {
    return new \atk4\ui\jsToast('Saving is disabled');
});
