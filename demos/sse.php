<?php

require 'init.php';

$btn1 = $app->add('Button')->set('start');
$btn2 = $app->add('Button')->set('start');

$sse1 = $btn1->add('SSE')->set(function () use ($btn1) {
    $btn1->set((string) rand(1, 100));
});

$btn1->on('click', $sse1->run());

$sse2 = $btn2->add('SSE')->set(function () use ($btn2) {
    $btn2->set((string) rand(1, 100));
});

$btn2->on('click', $sse2->run());

$app->add(['ui' => 'divider']);

$bar = $app->add(['View', 'template' => new \atk4\ui\Template('<div id="{$_id}" class="ui teal progress">
  <div class="bar"></div>
  <div class="label">Testing SSE</div>
</div>')]);
$bar->js(true)->progress();

$button = $app->add(['Button', 'Turn On']);
// non-SSE way
//$button->on('click', $bar->js()->progress(['percent'=> 40]));

$sse = $app->add('jsSSE');

$button->on('click', $sse->set(function () use ($sse, $bar) {
    $sse->send($bar->js()->progress(['percent' => 33]));
    sleep(1);
    $sse->send($bar->js()->progress(['percent' => 66]));
    sleep(1);

    // non-SSE way
    return $bar->js()->progress(['percent' => 100]);
}));
