<?php

require __DIR__ . '/init.php';

// create layout
$g = $app->add(new \atk4\ui\GridLayout(['columns'=>4, 'rows'=>2]));

// add other views in layout spots
$g->add(new \atk4\ui\LoremIpsum(['words'=>4]), 'r1c1'); // row 1, col 1
$g->add(new \atk4\ui\LoremIpsum(['words'=>4]), 'r1c4'); // row 1, col 4
$g->add(new \atk4\ui\LoremIpsum(['words'=>4]), 'r2c2'); // row 2, col 2
