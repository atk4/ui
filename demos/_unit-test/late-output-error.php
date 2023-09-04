<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Callback;
use Atk4\Ui\CallbackLater;
use Atk4\Ui\Header;
use Atk4\Ui\Modal;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$cbH1 = Callback::addTo($app);
$cbH1->setUrlTrigger('err_headers_already_sent_1');
$modalH1 = Modal::addTo($app, ['cb' => $cbH1]);
$modalH1->set(static function () {
    header('x-unmanaged-header: test');
    flush();
});

$cbO1 = Callback::addTo($app);
$cbO1->setUrlTrigger('err_unexpected_output_detected_1');
$modalO1 = Modal::addTo($app, ['cb' => $cbO1]);
$modalO1->set(static function () {
    // unexpected output can be detected only when output buffering is enabled and not flushed
    if (ob_get_level() === 0) {
        ob_start();
    }
    echo 'unmanaged output';
});

$cbH2 = CallbackLater::addTo($app);
$cbH2->setUrlTrigger('err_headers_already_sent_2');
$modalH2 = Modal::addTo($app, ['cb' => $cbH2]);
$modalH2->set($modalH1->fx);

$cbO2 = CallbackLater::addTo($app);
$cbO2->setUrlTrigger('err_unexpected_output_detected_2');
$modalO2 = Modal::addTo($app, ['cb' => $cbO2]);
$modalO2->set($modalO1->fx);

Header::addTo($app, ['content' => 'Before render (/w Callback)']);

$buttonH1 = Button::addTo($app, ['Test LateOutputError I: Headers already sent']);
$buttonH1->on('click', $modalH1->jsShow());

$buttonO1 = Button::addTo($app, ['Test LateOutputError I: Unexpected output detected']);
$buttonO1->on('click', $modalO1->jsShow());

Header::addTo($app, ['content' => 'After render (/w CallbackLater)']);

$buttonH2 = Button::addTo($app, ['Test LateOutputError II: Headers already sent']);
$buttonH2->on('click', $modalH2->jsShow());

$buttonO2 = Button::addTo($app, ['Test LateOutputError II: Unexpected output detected']);
$buttonO2->on('click', $modalO2->jsShow());
