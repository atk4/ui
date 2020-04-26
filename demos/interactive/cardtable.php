<?php

require_once __DIR__ . '/../atk-init.php';

\atk4\ui\Header::addTo($app, ['Card displays read-only data of a single record']);

\atk4\ui\CardTable::addTo($app)->setModel((new Stat($db))->tryLoadAny());
