<?php

require 'init.php';

$btn1 = $app->add('Button')->set('start');
$btn2 = $app->add('Button')->set('start');

$sse1 = $btn1->add('SSE')->set(function () use ($btn1) {
    $btn1->set((string)rand(1, 100));
});

$btn1->on('click', $sse1->run());

$sse2 = $btn2->add('SSE')->set(function () use ($btn2) {
    $btn2->set((string)rand(1, 100));
});

$btn2->on('click', $sse2->run());