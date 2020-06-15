<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/init-app.php';

\atk4\ui\Header::addTo($app)->set('Welcome to Agile Toolkit Demo!!');

$t = \atk4\ui\Text::addTo(\atk4\ui\View::addTo($app, [false, 'green', 'ui' => 'segment']));
$t->addParagraph('Take a quick stroll through some of the amazing features of Agile Toolkit.');

\atk4\ui\Button::addTo($app, ['Begin the demo..', 'huge primary fluid', 'iconRight' => 'right arrow'])
    ->link('tutorial/intro.php');

\atk4\ui\Header::addTo($app)->set('What is new in Agile Toolkit 2.0');

$t = \atk4\ui\Text::addTo(\atk4\ui\View::addTo($app, [false, 'green', 'ui' => 'segment']));
$t->addParagraph('In this version of Agile Toolkit we introduce "User Actions"!');

\atk4\ui\Button::addTo($app, ['Learn about User Actions', 'huge basic primary fluid', 'iconRight' => 'right arrow'])
    ->link('tutorial/actions.php');
