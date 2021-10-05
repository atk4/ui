<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\UserAction\PanelExecutor;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$factory = $app->getExecutorFactory();
$factory->registerTypeExecutor($factory::STEP_EXECUTOR, [PanelExecutor::class]);

require __DIR__ . '/jsactions2.php';
