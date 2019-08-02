<?php

require 'init.php';

// JUST TO TEST Exceptions and Error throws

$modal = $app->add(['Modal']);

$modal->set(function ($m) use ($modal) {
    throw new \Exception("TEST!");
});

$button = $app->add(['Button', 'Test modal exception']);
$button->on('click', $modal->show());

$modal2 = $app->add(['Modal']);

$modal2->set(function ($m) use ($modal2) {
    trigger_error("error triggered");
});

$button2 = $app->add(['Button', 'Test modal error']);
$button2->on('click', $modal2->show());
