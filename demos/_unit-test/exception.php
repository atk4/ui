<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\CallbackLater;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

// JUST TO TEST Exceptions and Error throws

$cb = CallbackLater::addTo($app);
$cb->setUrlTrigger('m_cb');

$modal = \atk4\ui\Modal::addTo($app, ['cb' => $cb]);
$modal->name = 'm_test';

$modal->set(function ($m) use ($modal) {
    throw new \Exception('TEST!');
});

$button = \atk4\ui\Button::addTo($app, ['Test modal exception']);
$button->on('click', $modal->show());

$cb1 = CallbackLater::addTo($app, ['urlTrigger' => 'm2_cb']);
$modal2 = \atk4\ui\Modal::addTo($app, ['cb' => $cb1]);

$modal2->set(function ($m) use ($modal2) {
    trigger_error('error triggered');
});

$button2 = \atk4\ui\Button::addTo($app, ['Test modal error']);
$button2->on('click', $modal2->show());
