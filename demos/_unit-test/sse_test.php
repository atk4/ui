<?php

namespace atk4\ui\demo;

require_once __DIR__ . '/../atk-init.php';

\atk4\ui\Header::addTo($app, ['SSE with ProgressBar']);

$bar = \atk4\ui\ProgressBar::addTo($app);

$button   = \atk4\ui\Button::addTo($app, ['Turn On']);
$btn_stop = \atk4\ui\Button::addTo($app, ['Turn Off']);
// non-SSE way
//$button->on('click', $bar->js()->progress(['percent'=> 40]));

$sse = \atk4\ui\jsSSE::addTo($app, ['showLoader' => true]);
// url trigger must match php_unit test in sse provider.
$sse->urlTrigger = 'see_test';

$button->on(
    'click',
    $sse->set(
        function () use ($button, $sse, $bar) {
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
        }
    )
);

$btn_stop->on('click', [$button->js()->atkServerEvent('stop'), $button->js()->removeClass('disabled')]);