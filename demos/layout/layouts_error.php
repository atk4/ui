<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Next line produces exception, which Agile UI will catch and display nicely.
\atk4\ui\View::addTo($app, ['foo' => 'bar']);
