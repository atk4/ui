<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

\Atk4\Ui\Header::addTo($app, ['Card displays read-only data of a single record']);

\Atk4\Ui\CardTable::addTo($app)->setModel((new Stat($app->db))->tryLoadAny());
