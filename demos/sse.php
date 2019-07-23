<?php

require 'init.php';

$app->add(['ui' => 'divider']);

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
        $button->js()->removeClass('disabled')
    ];
}));
