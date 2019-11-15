<?php

require 'init.php';

$app->add(['Header', 'SSE with ProgressBar']);

$bar = $app->add(['ProgressBar']);

$button = $app->add(['Button', 'Turn On']);
// non-SSE way
//$button->on('click', $bar->js()->progress(['percent'=> 40]));

$sse = $app->add(['jsSSE', 'showLoader' => true]);

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

$app->add(['ui' => 'divider']);
$app->add(['Header', 'SSE operation with user confirmation']);

$sse = $app->add(['jsSSE']);
$button = $app->add(['Button', 'Click me to change my text']);

$button->on('click', $sse->set(function($jsChain) use($sse, $button) {

    $sse->send($button->js()->text('Please wait for 2 seconds...'));
    sleep(2);

    return $button->js()->text($sse->args['newButtonText']);

}, ['newButtonText'=>'This is my new text!']), ['confirm'=>'Please confirm that you wish to continue']);
