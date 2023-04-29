<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Layout;
use Atk4\Ui\LoremIpsum;
use Atk4\Ui\Text;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

// nothing to do with Agile UI - will not use any Layout
$a = new LoremIpsum();
$text = $a->generateLorem(150);

$app->html = null;
$app->initLayout([Layout::class]);

Text::addTo($app->layout)->addParagraph($text);
