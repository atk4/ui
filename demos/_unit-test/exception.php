<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\CallbackLater;
use Atk4\Ui\Modal;

// test Exception and Error throw

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$cb = CallbackLater::addTo($app);
$cb->setUrlTrigger('m_cb');

$modal = Modal::addTo($app, ['cb' => $cb]);
$modal->name = 'm_test';

$modal->set(static function () {
    throw new \Exception('Test throw exception!');
});

$button = Button::addTo($app, ['Test modal exception']);
$button->on('click', $modal->jsShow());

$cb1 = CallbackLater::addTo($app, ['urlTrigger' => 'm2_cb']);
$modal2 = Modal::addTo($app, ['cb' => $cb1]);

$modal2->set(static function () {
    trigger_error('Test trigger error!');
});

$button2 = Button::addTo($app, ['Test modal error']);
$button2->on('click', $modal2->jsShow());
