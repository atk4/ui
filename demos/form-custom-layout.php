<?php
/**
 * Demonstrates how to use fields with form.
 */
require 'init.php';
require 'database.php';

$app->add(['Header', 'Custom Form Layout']);

$form = $app->add(['Form', 'layout'=>['Custom', 'defaultTemplate'=>__DIR__.'/form-custom-layout.html']]);
$form->setModel(new Stat($app->db));
