<?php

require_once __DIR__ . '/init.php';

$img = 'https://github.com/atk4/ui/raw/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';

$app->add(['Header', 'Message Types']);

$seg = $app->add(['ui' => 'raised segment']);

$bar_type = $seg->add(['ui' => ' basic buttons']);

$msg = $seg->add([
    'Message',
    'This is a title of your message',
    'type' => $app->stickyGet('type'),
    'icon' => $app->stickyGet('icon'),
]);
$msg->text->addParagraph('You can add some more text here for your messages');

$bar_type->on('click', '.button', new \atk4\ui\jsReload($seg, ['type' => (new \atk4\ui\jQuery())->text()]));
$bar_type->add(['Button', 'success']);
$bar_type->add(['Button', 'error']);
$bar_type->add(['Button', 'info']);
$bar_type->add(['Button', 'warning']);

$bar_icon = $seg->add(['View', 'ui' => ' basic buttons']);
$bar_icon->on('click', '.button', new \atk4\ui\jsReload($seg, ['icon' => (new \atk4\ui\jQuery())->find('i')->attr('class')]));
$bar_icon->add(['Button', 'icon' => 'book']);
$bar_icon->add(['Button', 'icon' => 'check circle outline']);
$bar_icon->add(['Button', 'icon' => 'pointing right']);
$bar_icon->add(['Button', 'icon' => 'asterisk loading']);
$bar_icon->add(['Button', 'icon' => 'vertically flipped cloud']);
