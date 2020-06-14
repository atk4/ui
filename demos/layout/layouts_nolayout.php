<?php

namespace atk4\ui\demo;

require_once __DIR__ . '/../atk-init.php';

// nothing to do with Agile UI - will not use any Layout
$a = new \atk4\ui\LoremIpsum();

echo htmlspecialchars($a->generateLorem(150));
