<?php
/**
 * Demonstrates how to use fields with form.
 */
require __DIR__ . '/init.php';
require __DIR__ . '/database.php';

$app->add(['Header', 'Custom Form Layout']);

$form = $app->add(['Form', 'layout'=>['Custom', 'defaultTemplate'=>__DIR__.'/form-custom-layout.html']]);
$form->setModel(new Stat($app->db));
