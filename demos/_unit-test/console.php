<?php

namespace atk4\ui\demo;

use atk4\ui\jsSSE;

require_once __DIR__ . '/../atk-init.php';

$sse = jsSSE::addTo($app);
$sse->urlTrigger = 'console_test';

$console = \atk4\ui\Console::addTo($app, ['sse' => $sse]);

$console->set(function ($console) {
    $console->output('Executing test process...');
    $console->output('Now trying something dangerous..');
    echo 'direct output is captured';

    throw new \atk4\core\Exception('BOOM - exceptions are caught');
});
