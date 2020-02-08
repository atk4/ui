<?php

include __DIR__ . '/init.php';
include __DIR__ . '/database.php';

$app->add(['Header', 'Card displays read-only data of a single record']);

$app->add(['CardTable'])->setModel((new Stat($db))->tryLoadAny());
