<?php
/**
 * Demonstrates how to use fields with form.
 */
chdir('..');
require_once dirname(__DIR__) . '/atk-init.php';

\atk4\ui\Header::addTo($app, ['Custom Form Layout']);

$form = \atk4\ui\Form::addTo($app, ['layout'=>['Custom', 'defaultTemplate'=>__DIR__ . '/form-custom-layout.html']]);
$form->setModel(new CountryLock($app->db))->loadAny();

$form->onSubmit(function ($f) {
    return new \atk4\ui\jsToast('Saving is disabled');
});
