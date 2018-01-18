<?php
/**
 * Demonstrates how to use tabs.
 */
require 'init.php';

$p = $app->add(['ProgressBar', 20]);

$p = $app->add(['ProgressBar', 60, 'indicating progress', 'indicating']);
$app->add(['Button', 'increment'])->on('click', $p->jsIncrement());
$app->add(['Button', 'set'])->on('click', $p->jsValue(20));

