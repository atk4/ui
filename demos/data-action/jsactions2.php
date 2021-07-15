<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\UserAction\ModalExecutor;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$factory = $app->getExecutorFactory();
$factory->registerTypeExecutor($factory::STEP_EXECUTOR, [ModalExecutor::class]);

require_once 'action-setup.php';
