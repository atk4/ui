<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\CardTable;
use Atk4\Ui\Header;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

Header::addTo($app, ['Card displays read-only data of a single record']);

$entity = (new Stat($app->db))->loadAny();
$entity->project_code .= ' <b>no reload</b>';

CardTable::addTo($app)->setModel($entity);

// CardTable uses internally atk4_local_object type which uses weak references,
// force GC to test the data are kept referenced correctly
gc_collect_cycles();
