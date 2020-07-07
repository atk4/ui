<?php

declare(strict_types=1);

namespace atk4\ui\demo;

/** @var \atk4\ui\App $app */
require_once __DIR__ . '/../init-app.php';

$img = 'https://github.com/atk4/ui/raw/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';

\atk4\ui\Header::addTo($app, ['Message Types']);

$seg = \atk4\ui\View::addTo($app, ['ui' => 'raised segment']);

$barType = \atk4\ui\View::addTo($seg, ['ui' => ' basic buttons']);

$msg = \atk4\ui\Message::addTo($seg, [
    'This is a title of your message',
    'type' => $app->stickyGet('type'),
    'icon' => $app->stickyGet('icon'),
]);
$msg->text->addParagraph('You can add some more text here for your messages');

$barType->on('click', '.button', new \atk4\ui\JsReload($seg, ['type' => (new \atk4\ui\Jquery())->text()]));
\atk4\ui\Button::addTo($barType, ['success']);
\atk4\ui\Button::addTo($barType, ['error']);
\atk4\ui\Button::addTo($barType, ['info']);
\atk4\ui\Button::addTo($barType, ['warning']);

$barIcon = \atk4\ui\View::addTo($seg, ['ui' => ' basic buttons']);
$barIcon->on('click', '.button', new \atk4\ui\JsReload($seg, ['icon' => (new \atk4\ui\Jquery())->find('i')->attr('class')]));
\atk4\ui\Button::addTo($barIcon, ['icon' => 'book']);
\atk4\ui\Button::addTo($barIcon, ['icon' => 'check circle outline']);
\atk4\ui\Button::addTo($barIcon, ['icon' => 'pointing right']);
\atk4\ui\Button::addTo($barIcon, ['icon' => 'asterisk loading']);
\atk4\ui\Button::addTo($barIcon, ['icon' => 'vertically flipped cloud']);
