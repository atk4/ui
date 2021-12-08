<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\CallbackLater;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Demo Testing of LateOutputErrors

$cb = CallbackLater::addTo($app);
$cb->setUrlTrigger('m_err_header_sent');

$modal = \Atk4\Ui\Modal::addTo($app, ['cb' => $cb]);
$modal->name = 'm_header';

$modal->set(function () {
    // force output of header
    ob_implicit_flush();
    header('test-error: unexpected output');
    flush();
});

$cb2 = CallbackLater::addTo($app);
$cb2->setUrlTrigger('m_err_unex_content');

$modal2 = \Atk4\Ui\Modal::addTo($app, ['cb' => $cb2]);
$modal2->name = 'm_header';

$modal2->set(function () {
    echo 'Unexpected output before emitResponse';
});

$button = \Atk4\Ui\Button::addTo($app, ['Test LateOutputError : Header already sent']);
$button->on('click', $modal->show());

$button2 = \Atk4\Ui\Button::addTo($app, ['Test LateOutputError : Unexpected output before emitResponse']);
$button2->on('click', $modal2->show());
