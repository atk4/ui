<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

// create layout
$gridLayout = \atk4\ui\GridLayout::addTo($app, ['columns' => 4, 'rows' => 2]);

// add other views in layout spots
\atk4\ui\LoremIpsum::addTo($gridLayout, ['words' => 4], ['r1c1']); // row 1, col 1
\atk4\ui\LoremIpsum::addTo($gridLayout, ['words' => 4], ['r1c4']); // row 1, col 4
\atk4\ui\LoremIpsum::addTo($gridLayout, ['words' => 4], ['r2c2']); // row 2, col 2
