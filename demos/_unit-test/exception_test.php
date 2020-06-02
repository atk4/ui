<?php

namespace atk4\ui\demo;

require_once __DIR__ . '/../atk-init.php';

// JUST TO TEST Exceptions and Error throws

$modal = \atk4\ui\Modal::addTo($app);

$modal->set(function ($m) use ($modal) {
    throw new \Exception('TEST!');
});

$button = \atk4\ui\Button::addTo($app, ['Test modal exception']);
$button->on('click', $modal->show());

$modal2 = \atk4\ui\Modal::addTo($app);

$modal2->set(function ($m) use ($modal2) {
    trigger_error('error triggered');
});

$button2 = \atk4\ui\Button::addTo($app, ['Test modal error']);
$button2->on('click', $modal2->show());
