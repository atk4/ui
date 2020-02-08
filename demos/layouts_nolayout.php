<?php

include_once __DIR__ . '/../vendor/autoload.php';

// nothing to do with Agile UI - will not use any Layout
$a = new \atk4\ui\LoremIpsum();

echo htmlspecialchars($a->generateLorem(150));
