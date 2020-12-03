<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\JsExpression;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$v = View::addTo($app)->set('This will trigger a network request for testing sse...');

$sse = \Atk4\Ui\JsSse::addTo($app);
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
