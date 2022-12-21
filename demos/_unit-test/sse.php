<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\JsExpression;
use Atk4\Ui\JsSse;
use Atk4\Ui\JsToast;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$v = View::addTo($app)->set('This will trigger a network request for testing SSE...');

$sse = JsSse::addTo($app);
// url trigger must match php_unit test in sse provider.
$sse->setUrlTrigger('see_test');

$v->js(true, $sse->set(function () use ($sse) {
    for ($i = 0; $i < 4; ++$i) {
        $sse->send(new JsExpression('console.log([])', ['test ' . $i]));
    }

    // non-SSE way
    return new JsToast('SSE sent, see browser console log');
})->jsExecute());
