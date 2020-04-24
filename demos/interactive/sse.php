<?php

chdir('..');

require_once 'atk-init.php';




\atk4\ui\Header::addTo($app, ['SSE with ProgressBar']);

$bar = \atk4\ui\ProgressBar::addTo($app);

$button = \atk4\ui\Button::addTo($app, ['Turn On']);
// non-SSE way
//$button->on('click', $bar->js()->progress(['percent'=> 40]));

$sse = \atk4\ui\jsSSE::addTo($app, ['showLoader' => true]);

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

\atk4\ui\View::addTo($app, ['ui' => 'divider']);
\atk4\ui\Header::addTo($app, ['SSE operation with user confirmation']);

$sse = \atk4\ui\jsSSE::addTo($app);
$button = \atk4\ui\Button::addTo($app, ['Click me to change my text']);

$button->on('click', $sse->set(function ($jsChain) use ($sse, $button) {
    $sse->send($button->js()->text('Please wait for 2 seconds...'));
    sleep(2);

    return $button->js()->text($sse->args['newButtonText']);
}, ['newButtonText'=>'This is my new text!']), ['confirm'=>'Please confirm that you wish to continue']);
