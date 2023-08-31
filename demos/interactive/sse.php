<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsBlock;
use Atk4\Ui\JsSse;
use Atk4\Ui\ProgressBar;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['SSE with ProgressBar']);

$bar = ProgressBar::addTo($app);

$button = Button::addTo($app, ['Turn On']);
$buttonStop = Button::addTo($app, ['Turn Off']);
// non-SSE way
// $button->on('click', $bar->js()->progress(['percent' => 40]));

$sse = JsSse::addTo($button, ['showLoader' => true]);

$button->on('click', $sse->set(static function () use ($button, $sse, $bar) {
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
    return new JsBlock([
        $bar->jsValue(100),
        $button->js()->removeClass('disabled'),
    ]);
}));

$buttonStop->on('click', new JsBlock([
    $button->js()->atkServerEvent('stop'),
    $button->js()->removeClass('disabled'),
]));

View::addTo($app, ['ui' => 'divider']);
Header::addTo($app, ['SSE operation with user confirmation']);

$sse = JsSse::addTo($app);
$button = Button::addTo($app, ['Click me to change my text']);

$button->on('click', $sse->set(static function (Jquery $jsChain, string $newButtonText) use ($sse, $button) {
    $sse->send($button->js()->text('Please wait for 2 seconds...'));
    sleep(2);

    return $button->js()->text($newButtonText);
}, ['newButtonText' => 'This is my new text!']), ['confirm' => 'Please confirm that you wish to continue']);
