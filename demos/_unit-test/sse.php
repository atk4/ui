<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\JsExpression;
use atk4\ui\View;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$v = View::addTo($app)->set('This will trigger a network request for testing sse...');

$sse = \atk4\ui\JsSse::addTo($app);
// url trigger must match php_unit test in sse provider.
$sse->setUrlTrigger('see_test');

$v->js(true, $sse->set(function () use ($sse) {
    $sse->send(new JsExpression('console.log("test")'));
    $sse->send(new JsExpression('console.log("test")'));
    $sse->send(new JsExpression('console.log("test")'));
    $sse->send(new JsExpression('console.log("test")'));

    // non-SSE way
    return $sse->send(new JsExpression('console.log("test")'));
}));
