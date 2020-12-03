<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// create layout
$gridLayout = \Atk4\Ui\GridLayout::addTo($app, ['columns' => 4, 'rows' => 2]);

// add other views in layout spots
\Atk4\Ui\LoremIpsum::addTo($gridLayout, ['words' => 4], ['r1c1']); // row 1, col 1
\Atk4\Ui\LoremIpsum::addTo($gridLayout, ['words' => 4], ['r1c4']); // row 1, col 4
\Atk4\Ui\LoremIpsum::addTo($gridLayout, ['words' => 4], ['r2c2']); // row 2, col 2
