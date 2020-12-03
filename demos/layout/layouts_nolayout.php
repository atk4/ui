<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// nothing to do with Agile UI - will not use any Layout
$a = new \Atk4\Ui\LoremIpsum();
$text = $a->generateLorem(150);

$app->html = null;
$app->initLayout([\Atk4\Ui\Layout::class]);

\Atk4\Ui\Text::addTo($app->layout)->addParagraph($text);
