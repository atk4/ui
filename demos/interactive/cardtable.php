<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\CardTable;
use Atk4\Ui\Header;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Card displays read-only data of a single record']);

CardTable::addTo($app)->setModel((new Stat($app->db))->loadAny());
