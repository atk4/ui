<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Header;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$img = $app->cdn['atk'] . '/logo.png';

Header::addTo($app, ['Message Types']);

$seg = \Atk4\Ui\View::addTo($app, ['ui' => 'raised segment']);

$barType = \Atk4\Ui\View::addTo($seg, ['ui' => ' basic buttons']);

$msg = \Atk4\Ui\Message::addTo($seg, [
    'This is a title of your message',
    'type' => $seg->stickyGet('type'),
    'icon' => $seg->stickyGet('icon'),
]);
$msg->text->addParagraph('You can add some more text here for your messages');

$barType->on('click', '.button', new \Atk4\Ui\JsReload($seg, ['type' => (new \Atk4\Ui\Jquery())->text()]));
\Atk4\Ui\Button::addTo($barType, ['success']);
\Atk4\Ui\Button::addTo($barType, ['error']);
\Atk4\Ui\Button::addTo($barType, ['info']);
\Atk4\Ui\Button::addTo($barType, ['warning']);

$barIcon = \Atk4\Ui\View::addTo($seg, ['ui' => ' basic buttons']);
$barIcon->on('click', '.button', new \Atk4\Ui\JsReload($seg, ['icon' => (new \Atk4\Ui\Jquery())->find('i')->attr('class')]));
\Atk4\Ui\Button::addTo($barIcon, ['icon' => 'book']);
\Atk4\Ui\Button::addTo($barIcon, ['icon' => 'check circle outline']);
\Atk4\Ui\Button::addTo($barIcon, ['icon' => 'pointing right']);
\Atk4\Ui\Button::addTo($barIcon, ['icon' => 'asterisk loading']);
\Atk4\Ui\Button::addTo($barIcon, ['icon' => 'vertically flipped cloud']);
