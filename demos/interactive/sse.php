<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Header::addTo($app, ['SSE with ProgressBar']);

$bar = \Atk4\Ui\ProgressBar::addTo($app);

$button = \Atk4\Ui\Button::addTo($app, ['Turn On']);
$buttonStop = \Atk4\Ui\Button::addTo($app, ['Turn Off']);
// non-SSE way
//$button->on('click', $bar->js()->progress(['percent'=> 40]));

$sse = \Atk4\Ui\JsSse::addTo($app, ['showLoader' => true]);

$button->on('click', $sse->set(function () use ($button, $sse, $bar) {
    $sse->send($button->js()->addClass('disabled'));

    $sse->send($bar->jsValue(20));
    sleep(1);
    $sse->send($bar->jsValue(40));
    sleep(1);
    $sse->send($bar->jsValue(60));
    sleep(2);
    $sse->send($bar->jsValue(80));
    sleep(1);

    // non-SSE way
    return [
        $bar->jsValue(100),
        $button->js()->removeClass('disabled'),
    ];
}));

$buttonStop->on('click', [$button->js()->atkServerEvent('stop'), $button->js()->removeClass('disabled')]);

\Atk4\Ui\View::addTo($app, ['ui' => 'divider']);
\Atk4\Ui\Header::addTo($app, ['SSE operation with user confirmation']);

$sse = \Atk4\Ui\JsSse::addTo($app);
$button = \Atk4\Ui\Button::addTo($app, ['Click me to change my text']);

$button->on('click', $sse->set(function ($jsChain) use ($sse, $button) {
    $sse->send($button->js()->text('Please wait for 2 seconds...'));
    sleep(2);

    return $button->js()->text($sse->args['newButtonText']);
}, ['newButtonText' => 'This is my new text!']), ['confirm' => 'Please confirm that you wish to continue']);
