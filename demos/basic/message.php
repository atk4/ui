<?php



namespace atk4\ui\demo;

require_once __DIR__ . '/../atk-init.php';

$img = 'https://github.com/atk4/ui/raw/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';

\atk4\ui\Header::addTo($app, ['Message Types']);

$seg = \atk4\ui\View::addTo($app, ['ui' => 'raised segment']);

$bar_type = \atk4\ui\View::addTo($seg, ['ui' => ' basic buttons']);

$msg = \atk4\ui\Message::addTo($seg, [
    'This is a title of your message',
    'type' => $app->stickyGet('type'),
    'icon' => $app->stickyGet('icon'),
]);
$msg->text->addParagraph('You can add some more text here for your messages');

$bar_type->on('click', '.button', new \atk4\ui\jsReload($seg, ['type' => (new \atk4\ui\jQuery())->text()]));
\atk4\ui\Button::addTo($bar_type, ['success']);
\atk4\ui\Button::addTo($bar_type, ['error']);
\atk4\ui\Button::addTo($bar_type, ['info']);
\atk4\ui\Button::addTo($bar_type, ['warning']);

$bar_icon = \atk4\ui\View::addTo($seg, ['ui' => ' basic buttons']);
$bar_icon->on('click', '.button', new \atk4\ui\jsReload($seg, ['icon' => (new \atk4\ui\jQuery())->find('i')->attr('class')]));
\atk4\ui\Button::addTo($bar_icon, ['icon' => 'book']);
\atk4\ui\Button::addTo($bar_icon, ['icon' => 'check circle outline']);
\atk4\ui\Button::addTo($bar_icon, ['icon' => 'pointing right']);
\atk4\ui\Button::addTo($bar_icon, ['icon' => 'asterisk loading']);
\atk4\ui\Button::addTo($bar_icon, ['icon' => 'vertically flipped cloud']);
