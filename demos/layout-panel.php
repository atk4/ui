<?php

require_once __DIR__ . '/init.php';


$panel_1 = $app->layout->addRightPanel(new \atk4\ui\Panel\Slide());
\atk4\ui\LoremIpsum::addTo($panel_1, ['size' => 1, 'words' => 12]);

$panel_2 = $app->layout->addRightPanel(new \atk4\ui\Panel\Slide());

$btn = \atk4\ui\Button::addTo($app, ['Button 1']);
$btn->js(true)->data('btn', '1');
$btn->on('click', $panel_1->jsOpen(new \atk4\ui\jQuery(), ['btn'], 'orange'));

$btn = \atk4\ui\Button::addTo($app, ['Button 2']);
$btn->js(true)->data('btn', '2');
$btn->on('click', $panel_1->jsOpen(new \atk4\ui\jQuery(), ['btn'], 'orange'));

$panel_1->onOpen(function($p) {
    $btn_number = $_GET['btn'] ?? null;
    $text =  'You loaded panel content using button #' . $btn_number;
    $msg = \atk4\ui\Message::addTo($p, ['Panel 1', 'text' => $text]);
//    $lorem = \atk4\ui\LoremIpsum::addTo($p, ['size' => 12, 'words' => 15]);
});



