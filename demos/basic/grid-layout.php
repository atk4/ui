<?php

chdir('..');
require_once dirname(__DIR__) . '/atk-init.php';

// create layout
$g = \atk4\ui\GridLayout::addTo($app, ['columns'=>4, 'rows'=>2]);

// add other views in layout spots
\atk4\ui\LoremIpsum::addTo($g, ['words'=>4], ['r1c1']); // row 1, col 1
\atk4\ui\LoremIpsum::addTo($g, ['words'=>4], ['r1c4']); // row 1, col 4
\atk4\ui\LoremIpsum::addTo($g, ['words'=>4], ['r2c2']); // row 2, col 2
