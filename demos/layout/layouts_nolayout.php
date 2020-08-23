<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

// nothing to do with Agile UI - will not use any Layout
$a = new \atk4\ui\LoremIpsum();
$text = $a->generateLorem(150);

$app->html = null;
$app->initLayout([\atk4\ui\Layout::class]);

\atk4\ui\Text::addTo($app->layout)->addParagraph($text);
