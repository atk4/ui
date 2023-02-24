<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\Text;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/init-app.php';

Header::addTo($app)->set('Welcome to Agile Toolkit Demo!');

$t = Text::addTo(View::addTo($app, ['class.green' => true, 'ui' => 'segment']));
$t->addParagraph('Take a quick stroll through some of the amazing features of Agile Toolkit.');

Button::addTo($app, ['Begin the demo..', 'class.huge primary fluid' => true, 'iconRight' => 'right arrow'])
    ->link('tutorial/intro.php');

Header::addTo($app)->set('What is new in Agile Toolkit');

$t = Text::addTo(View::addTo($app, ['class.green' => true, 'ui' => 'segment']));
$t->addParagraph('In this version of Agile Toolkit we introduce "User Actions"!');

Button::addTo($app, ['Learn about User Actions', 'class.huge basic primary fluid' => true, 'iconRight' => 'right arrow'])
    ->link('tutorial/actions.php');
