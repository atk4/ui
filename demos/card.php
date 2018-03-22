<?php

include 'init.php';
include 'database.php';

$app->add(['Header', 'Card displays read-only data of a single record']);

$app->add(['Card'])->setModel((new Stat($db))->tryLoadAny());
