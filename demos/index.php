<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/init-app.php';

\Atk4\Ui\Header::addTo($app)->set('Welcome to Agile Toolkit Demo!!');

$t = \Atk4\Ui\Text::addTo(\Atk4\Ui\View::addTo($app, [false, 'class.green' => true, 'ui' => 'segment']));
$t->addParagraph('Take a quick stroll through some of the amazing features of Agile Toolkit.');

\Atk4\Ui\Button::addTo($app, ['Begin the demo..', 'class.huge primary fluid' => true, 'iconRight' => 'right arrow'])
    ->link('tutorial/intro.php');

\Atk4\Ui\Header::addTo($app)->set('What is new in Agile Toolkit 2.0');

$t = \Atk4\Ui\Text::addTo(\Atk4\Ui\View::addTo($app, [false, 'class.green' => true, 'ui' => 'segment']));
$t->addParagraph('In this version of Agile Toolkit we introduce "User Actions"!');

\Atk4\Ui\Button::addTo($app, ['Learn about User Actions', 'class.huge basic primary fluid' => true, 'iconRight' => 'right arrow'])
    ->link('tutorial/actions.php');
