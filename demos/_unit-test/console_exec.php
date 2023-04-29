<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Console;
use Atk4\Ui\JsSse;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$sse = JsSse::addTo($app);
$sse->setUrlTrigger('console_test');

$console = Console::addTo($app, ['sse' => $sse]);
$console->exec('/bin/pwd');
