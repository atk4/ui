<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Callback;
use Atk4\Ui\Header;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$app->catchRunawayCallbacks = false;

$expectedWord = <<<'EOF'
    v3 url: callback_url.php /
    callback url: callback_url.php?__atk_cb_test=callback&__atk_cbtarget=test /
    v2 url: callback_url.php
    EOF;

$b1 = Button::addTo($app)->set('callback');

$v1 = View::addTo($app);
$cb = Callback::addTo($v1, ['urlTrigger' => 'test']);
$v2 = View::addTo($v1);

$cb->set(function () use ($v1, $cb, $app, $v2) {
    $v3 = View::addTo($v1);
    Header::addTo($app, ['Result:']);
    $result = 'v3 url: ' . $v3->url() . ' / ' . 'callback url: ' . $cb->getUrl() . ' / ' . 'v2 url: ' . $v2->url();
    View::addTo($app, ['element' => 'p', 'content' => $result])->addClass('atk-result');
});

$b1->link($cb->getUrl());

Header::addTo($app, ['Expected:']);
View::addTo($app, ['element' => 'p', 'content' => $expectedWord])->addClass('atk-expected');
