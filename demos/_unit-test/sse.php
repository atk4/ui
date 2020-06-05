<?php

namespace atk4\ui\demo;

use atk4\ui\jsExpression;
use atk4\ui\View;

require_once __DIR__ . '/../atk-init.php';

$v = View::addTo($app)->set('This will trigger a network request for testing sse...');

$sse = \atk4\ui\jsSSE::addTo($app);
// url trigger must match php_unit test in sse provider.
$sse->urlTrigger = 'see_test';

$v->js(true, $sse->set(function () use ($sse) {
    $sse->send(new jsExpression('console.log("test")'));
    $sse->send(new jsExpression('console.log("test")'));
    $sse->send(new jsExpression('console.log("test")'));
    $sse->send(new jsExpression('console.log("test")'));

    // non-SSE way
    return $sse->send(new jsExpression('console.log("test")'));
}));
