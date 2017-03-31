<?php

require 'init.php';

$img = 'https://github.com/atk4/ui/raw/07208a0af84109f0d6e3553e242720d8aeedb784/public/logo.png';

$layout->add(['Header', 'Message Types']);
$bar_type = $layout->add(['View', 'ui'=>' basic buttons']);

$bar_type = $layout->add(['View', 'ui'=>' basic buttons']);

$msg = $layout->add([
    'Message',
    'This is a title of your message',
    'type' => isset($_GET['type']) ? $_GET['type'] : null,
    'icon' => isset($_GET['icon']) ? $_GET['icon'] : null,
]);
$msg->text->addParagraph('You can add some more text here for your messages');

$bar_type->on('click', '.button', new \atk4\ui\jsReload($msg, ['type'=>(new \atk4\ui\jQuery())->text()]));
$bar_type->add(['Button', 'success']);
$bar_type->add(['Button', 'error']);
$bar_type->add(['Button', 'info']);
$bar_type->add(['Button', 'warning']);

$bar_icon = $layout->add(['View', 'ui'=>' basic buttons']);
$bar_icon->on('click', '.button', new \atk4\ui\jsReload($msg, ['icon'=>(new \atk4\ui\jQuery())->find('i')->attr('class')]));
$bar_icon->add(['Button', 'icon'=>'book']);
$bar_icon->add(['Button', 'icon'=>'check circle outline']);
$bar_icon->add(['Button', 'icon'=>'pointing right']);
$bar_icon->add(['Button', 'icon'=>'asterisk loading']);
$bar_icon->add(['Button', 'icon'=>'vertically flipped cloud']);
