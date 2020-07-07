<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\jsSse;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$sse = jsSse::addTo($app);
$sse->urlTrigger = 'console_test';

$console = \atk4\ui\Console::addTo($app, ['sse' => $sse]);
$console->exec('/bin/pwd');
