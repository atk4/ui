<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\CallbackLater;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// JUST TO TEST Exceptions and Error throws

$cb = CallbackLater::addTo($app);
$cb->setUrlTrigger('m_cb');

$modal = \Atk4\Ui\Modal::addTo($app, ['cb' => $cb]);
$modal->name = 'm_test';

$modal->set(function ($m) {
    throw new \Exception('TEST!');
});

$button = \Atk4\Ui\Button::addTo($app, ['Test modal exception']);
$button->on('click', $modal->show());

$cb1 = CallbackLater::addTo($app, ['urlTrigger' => 'm2_cb']);
$modal2 = \Atk4\Ui\Modal::addTo($app, ['cb' => $cb1]);

$modal2->set(function ($m) {
    trigger_error('error triggered');
});

$button2 = \Atk4\Ui\Button::addTo($app, ['Test modal error']);
$button2->on('click', $modal2->show());
