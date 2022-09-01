<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\CardDeck;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$model = new Country($app->db);
$deck = CardDeck::addTo($app);
$deck->setModel($model, [$model->fieldName()->iso]);
