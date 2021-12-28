<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\CallbackLater;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$cb = CallbackLater::addTo($app);
$cb->setUrlTrigger('err_headers_already_sent');

$modal = \Atk4\Ui\Modal::addTo($app, ['cb' => $cb]);
$modal->set(function () {
    header('x-unmanaged-header: test');
    flush();
});

$cb2 = CallbackLater::addTo($app);
$cb2->setUrlTrigger('err_unexpected_output_detected');

$modal2 = \Atk4\Ui\Modal::addTo($app, ['cb' => $cb2]);
$modal2->set(function () {
    // unexpected output can be detected only when output buffering is enabled and not flushed
    if (ob_get_level() === 0) {
        ob_start();
    }
    echo 'unmanaged output';
});

$button = \Atk4\Ui\Button::addTo($app, ['Test LateOutputError: Headers already sent']);
$button->on('click', $modal->show());

$button2 = \Atk4\Ui\Button::addTo($app, ['Test LateOutputError: Unexpected output detected']);
$button2->on('click', $modal2->show());
