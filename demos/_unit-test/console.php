<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\JsSse;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$sse = JsSse::addTo($app);
$sse->setUrlTrigger('console_test');

$console = \Atk4\Ui\Console::addTo($app, ['sse' => $sse]);

$console->set(function ($console) {
    $console->output('Executing test process...');
    $console->output('Now trying something dangerous..');
    echo 'direct output is captured';

    throw new \Atk4\Core\Exception('BOOM - exceptions are caught');
});
