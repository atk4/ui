<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// next line produces exception, which Agile UI will catch and display nicely
View::addTo($app, ['foo' => 'bar']);
