<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Core\Exception as CoreException;
use Atk4\Ui\Console;
use Atk4\Ui\JsSse;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$sse = JsSse::addTo($app);
$sse->setUrlTrigger('console_test');

$console = Console::addTo($app, ['sse' => $sse]);

$console->set(static function (Console $console) {
    $console->output('Executing test process...');
    $console->output('Now trying something dangerous..');
    echo 'direct output is captured';

    throw new CoreException('BOOM - exceptions are caught');
});
