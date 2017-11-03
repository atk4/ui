<?php

require 'init.php';

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
    $sse->send($bar->js()->progress(['percent' => 20]));
    sleep(0.5);
    $sse->send($bar->js()->progress(['percent' => 40]));
    sleep(1);
    $sse->send($bar->js()->progress(['percent' => 60]));
    sleep(2);
    $sse->send($bar->js()->progress(['percent' => 80]));
    sleep(1);

    // non-SSE way
    return $bar->js()->progress(['percent' => 100]);
}));
