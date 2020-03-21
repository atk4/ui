<?php
/**
 * Demonstrates how to use fields with form.
 */
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

\atk4\ui\Header::addTo($app, ['Custom Form Layout']);

$form = \atk4\ui\Form::addTo($app, ['layout'=>['Custom', 'defaultTemplate'=>__DIR__.'/form-custom-layout.html']]);
$form->setModel(new Stat($app->db));
