<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

\atk4\ui\Header::addTo($app, ['Card displays read-only data of a single record']);

\atk4\ui\CardTable::addTo($app)->setModel((new Stat($app->db))->tryLoadAny());
