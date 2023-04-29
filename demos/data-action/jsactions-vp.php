<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\UserAction\ExecutorFactory;
use Atk4\Ui\UserAction\VpExecutor;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$factory = $app->getExecutorFactory();
$factory->registerTypeExecutor(ExecutorFactory::STEP_EXECUTOR, [VpExecutor::class]);

require_once 'jsactions2.php';
