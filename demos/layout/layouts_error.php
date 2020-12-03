<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// Next line produces exception, which Agile UI will catch and display nicely.
\Atk4\Ui\View::addTo($app, ['foo' => 'bar']);
