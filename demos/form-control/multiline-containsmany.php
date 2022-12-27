<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Crud;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Crud::addTo($app)->setModel(new MultilineDelivery($app->db));
