<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\App;
use Atk4\Ui\View;

/** @var App $app */
require_once __DIR__ . '/../init-app.php';

$app->setResponseHeader('Cache-Control', ''); // test if no-store header is sent even if removed

// next line produces exception, which Agile UI will catch and display nicely
View::addTo($app, ['foo' => 'bar']);
