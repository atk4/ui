<?php
/**
 * Testing form.
 */
require 'init.php';
require 'database.php';

$layout->add(['Header', 'Testing CRUD-friendly from implementation']);


$form = $layout->add('Form');
$form->setModel(new Stat($db))->loadAny();

