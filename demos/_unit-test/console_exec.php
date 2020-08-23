<?php

declare(strict_types=1);

namespace atk4\ui\demo;

use atk4\ui\JsSse;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$sse = JsSse::addTo($app);
$sse->setUrlTrigger('console_test');

$console = \atk4\ui\Console::addTo($app, ['sse' => $sse]);
$console->exec('/bin/pwd');
