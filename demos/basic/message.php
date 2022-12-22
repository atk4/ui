<?php

declare(strict_types=1);

namespace Atk4\Ui\Demos;

use Atk4\Ui\Button;
use Atk4\Ui\Header;
use Atk4\Ui\Js\Jquery;
use Atk4\Ui\Js\JsReload;
use Atk4\Ui\Message;
use Atk4\Ui\View;

/** @var \Atk4\Ui\App $app */
require_once __DIR__ . '/../init-app.php';

$img = $app->cdn['atk'] . '/logo.png';

Header::addTo($app, ['Message Types']);

$seg = View::addTo($app, ['ui' => 'raised segment']);

$barType = View::addTo($seg, ['ui' => ' basic buttons']);

$msg = Message::addTo($seg, [
    'This is a title of your message',
    'type' => $seg->stickyGet('type'),
    'icon' => $seg->stickyGet('icon'),
]);
$msg->text->addParagraph('You can add some more text here for your messages');

$barType->on('click', '.button', new JsReload($seg, ['type' => (new Jquery())->text()]));
Button::addTo($barType, ['success']);
Button::addTo($barType, ['error']);
Button::addTo($barType, ['info']);
Button::addTo($barType, ['warning']);

$barIcon = View::addTo($seg, ['ui' => ' basic buttons']);
$barIcon->on('click', '.button', new JsReload($seg, ['icon' => (new Jquery())->find('i')->attr('class')]));
Button::addTo($barIcon, ['icon' => 'book']);
Button::addTo($barIcon, ['icon' => 'check circle outline']);
Button::addTo($barIcon, ['icon' => 'pointing right']);
Button::addTo($barIcon, ['icon' => 'asterisk loading']);
Button::addTo($barIcon, ['icon' => 'vertically flipped cloud']);
